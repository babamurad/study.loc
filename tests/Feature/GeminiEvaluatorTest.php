<?php

use App\Contracts\PracticeEvaluatorInterface;
use App\Models\Practice;
use App\Models\PracticeSubmission;
use App\Models\PracticeTestCase;
use App\Models\PracticeTestResult;
use App\Models\User;
use App\Services\Evaluators\GeminiEvaluator;
use App\Services\SubmissionScoringService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    Config::set('services.gemini.api_key', 'test-gemini-key');
    Config::set('services.gemini.model', 'gemini-2.5-flash');
    Config::set('services.gemini.timeout', 15);

    $this->user = User::factory()->create();
    $this->practice = Practice::factory()->create([
        'max_score' => 10.0,
        'pass_score' => 7.0,
    ]);

    $this->testCase1 = PracticeTestCase::factory()->create([
        'practice_id' => $this->practice->id,
        'name' => 'Проверка наличия карточки',
        'type' => 'dom',
        'weight' => 5.0,
        'is_required' => true,
        'script' => ['selector' => '.card', 'exists' => true],
    ]);

    $this->testCase2 = PracticeTestCase::factory()->create([
        'practice_id' => $this->practice->id,
        'name' => 'Проверка цвета фона',
        'type' => 'css',
        'weight' => 5.0,
        'is_required' => false,
        'script' => ['selector' => '.card', 'property' => 'background-color', 'value' => 'red'],
    ]);

    $this->submission = PracticeSubmission::factory()->create([
        'user_id' => $this->user->id,
        'practice_id' => $this->practice->id,
        'html_code' => '<div class="card"><h2>Заголовок</h2></div>',
        'css_code' => '.card { background-color: red; width: 300px; }',
        'js_code' => null,
        'status' => PracticeSubmission::STATUS_PENDING,
        'score' => null,
        'passed' => false,
        'attempt_no' => 1,
    ]);

    foreach ([$this->testCase1, $this->testCase2] as $tc) {
        PracticeTestResult::create([
            'practice_submission_id' => $this->submission->id,
            'practice_test_case_id' => $tc->id,
            'passed' => false,
            'earned_weight' => 0.0,
            'duration_ms' => 0,
            'message' => null,
        ]);
    }
});

function mockGeminiResponse(string $responseText): void
{
    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => $responseText],
                        ],
                    ],
                ],
            ],
        ], 200),
    ]);
}

describe('GeminiEvaluator parsing', function () {

    test('parses valid Gemini response with all tests passed', function () {
        mockGeminiResponse(json_encode([
            'results' => [
                ['id' => $this->testCase1->id, 'passed' => true, 'message' => 'Элемент .card успешно найден.'],
                ['id' => $this->testCase2->id, 'passed' => true, 'message' => 'Цвет фона red корректно задан.'],
            ],
        ]));

        $evaluator = new GeminiEvaluator();
        $result = $evaluator->evaluate($this->submission);

        expect($result['status'])->toBe('completed');
        expect($result['results'])->toHaveCount(2);

        $result1 = collect($result['results'])->firstWhere('id', $this->testCase1->id);
        expect($result1['passed'])->toBeTrue();
        expect($result1['message'])->toBe('Элемент .card успешно найден.');

        $result2 = collect($result['results'])->firstWhere('id', $this->testCase2->id);
        expect($result2['passed'])->toBeTrue();
        expect($result2['message'])->toBe('Цвет фона red корректно задан.');
    });

    test('parses valid Gemini response with mixed results', function () {
        mockGeminiResponse(json_encode([
            'results' => [
                ['id' => $this->testCase1->id, 'passed' => true, 'message' => 'Элемент .card успешно найден.'],
                ['id' => $this->testCase2->id, 'passed' => false, 'message' => 'В CSS не задан цвет фона red для .card. Найден цвет blue.'],
            ],
        ]));

        $evaluator = new GeminiEvaluator();
        $result = $evaluator->evaluate($this->submission);

        $result1 = collect($result['results'])->firstWhere('id', $this->testCase1->id);
        expect($result1['passed'])->toBeTrue();
        expect($result1['message'])->toBe('Элемент .card успешно найден.');

        $result2 = collect($result['results'])->firstWhere('id', $this->testCase2->id);
        expect($result2['passed'])->toBeFalse();
        expect($result2['message'])->toBe('В CSS не задан цвет фона red для .card. Найден цвет blue.');
    });

    test('returns failed result when Gemini response is missing a test case', function () {
        mockGeminiResponse(json_encode([
            'results' => [
                ['id' => $this->testCase1->id, 'passed' => true, 'message' => 'Пройдено.'],
            ],
        ]));

        $evaluator = new GeminiEvaluator();
        $result = $evaluator->evaluate($this->submission);

        expect($result['results'])->toHaveCount(2);

        $missing = collect($result['results'])->firstWhere('id', $this->testCase2->id);
        expect($missing['passed'])->toBeFalse();
        expect($missing['message'])->toBe('Тест-кейс не был проверен системой.');
    });

    test('handles invalid JSON response from Gemini', function () {
        mockGeminiResponse('Это не JSON');

        $evaluator = new GeminiEvaluator();
        $result = $evaluator->evaluate($this->submission);

        expect($result['status'])->toBe('completed');
        expect($result['results'])->toHaveCount(2);

        foreach ($result['results'] as $r) {
            expect($r['passed'])->toBeFalse();
            expect($r['message'])->toContain('Ошибка проверяющей системы');
        }
    });

    test('handles empty Gemini response', function () {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    ['content' => ['parts' => []]],
                ],
            ], 200),
        ]);

        $evaluator = new GeminiEvaluator();
        $result = $evaluator->evaluate($this->submission);

        expect($result['results'])->toHaveCount(2);
        foreach ($result['results'] as $r) {
            expect($r['passed'])->toBeFalse();
        }
    });

    test('handles API timeout', function () {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response(null, 408),
        ]);

        $evaluator = new GeminiEvaluator();

        $this->expectException(\RuntimeException::class);
        $evaluator->evaluate($this->submission);
    });

    test('handles 429 rate limit', function () {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response(null, 429),
        ]);

        $evaluator = new GeminiEvaluator();

        try {
            $evaluator->evaluate($this->submission);
        } catch (\RuntimeException $e) {
            expect($e->getCode())->toBe(429);
            expect($e->getMessage())->toContain('Превышен лимит запросов');
        }
    });

});

describe('GeminiEvaluator integration with SubmissionScoringService', function () {

    test('processResults correctly maps Gemini results to database', function () {
        mockGeminiResponse(json_encode([
            'results' => [
                ['id' => $this->testCase1->id, 'passed' => true, 'message' => 'Элемент .card найден.'],
                ['id' => $this->testCase2->id, 'passed' => false, 'message' => 'Цвет не совпадает.'],
            ],
        ]));

        $evaluator = new GeminiEvaluator();
        $runnerResult = $evaluator->evaluate($this->submission);

        $scoringService = app(SubmissionScoringService::class);
        $scoringService->processResults($this->submission, $runnerResult);

        $this->submission->refresh();

        expect($this->submission->status)->toBe(PracticeSubmission::STATUS_COMPLETED);
        expect((float) $this->submission->score)->toEqual(5.0);
        expect($this->submission->passed)->toBeFalse();
        expect($this->submission->checked_at)->not->toBeNull();
        expect($this->submission->raw_result['results'])->toHaveCount(2);

        $result1 = PracticeTestResult::where('practice_test_case_id', $this->testCase1->id)
            ->where('practice_submission_id', $this->submission->id)
            ->first();
        expect($result1->passed)->toBeTrue();
        expect((float) $result1->earned_weight)->toEqual(5.0);
        expect($result1->message)->toBe('Элемент .card найден.');

        $result2 = PracticeTestResult::where('practice_test_case_id', $this->testCase2->id)
            ->where('practice_submission_id', $this->submission->id)
            ->first();
        expect($result2->passed)->toBeFalse();
        expect((float) $result2->earned_weight)->toEqual(0.0);
        expect($result2->message)->toBe('Цвет не совпадает.');
    });

    test('processResults marks submission as passed when all required tests pass', function () {
        mockGeminiResponse(json_encode([
            'results' => [
                ['id' => $this->testCase1->id, 'passed' => true, 'message' => 'Элемент .card найден.'],
                ['id' => $this->testCase2->id, 'passed' => true, 'message' => 'Цвет совпадает.'],
            ],
        ]));

        $evaluator = new GeminiEvaluator();
        $runnerResult = $evaluator->evaluate($this->submission);

        $scoringService = app(SubmissionScoringService::class);
        $scoringService->processResults($this->submission, $runnerResult);

        $this->submission->refresh();

        expect($this->submission->passed)->toBeTrue();
        expect((float) $this->submission->score)->toEqual(10.0);
    });

    test('full integration via PracticeEvaluatorInterface binding', function () {
        Config::set('services.practice_evaluator.driver', 'gemini');

        mockGeminiResponse(json_encode([
            'results' => [
                ['id' => $this->testCase1->id, 'passed' => true, 'message' => 'OK'],
                ['id' => $this->testCase2->id, 'passed' => true, 'message' => 'OK'],
            ],
        ]));

        app()->register(\App\Providers\AppServiceProvider::class);
        $evaluator = app(PracticeEvaluatorInterface::class);

        expect($evaluator)->toBeInstanceOf(GeminiEvaluator::class);

        $result = $evaluator->evaluate($this->submission);
        expect($result['status'])->toBe('completed');
        expect($result['results'])->toHaveCount(2);
    });

});
