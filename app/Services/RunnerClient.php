<?php

namespace App\Services;

use App\Models\LessonPractice;
use App\Models\PracticeSubmission;
use App\Models\PracticeTestCase;
use App\Models\PracticeTestResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RunnerClient
{
    private string $baseUrl;
    private string $apiKey;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.runner.url', 'http://localhost:8080');
        $this->apiKey = config('services.runner.api_key', 'test-key');
        $this->timeout = config('services.runner.timeout', 5000);
    }

    public function evaluate(PracticeSubmission $submission): array
    {
        $practice = $submission->lessonPractice;
        $testCases = $practice->testCases;

        $payload = [
            'submission_id' => $submission->id,
            'idempotency_key' => "{$submission->user_id}-{$submission->lesson_practice_id}-{$submission->attempt_no}",
            'profile' => $practice->runner_profile,
            'code' => [
                'html' => $submission->html_code,
                'css' => $submission->css_code,
                'js' => $submission->js_code,
            ],
            'tests' => $testCases->map(fn(PracticeTestCase $tc) => [
                'id' => $tc->id,
                'name' => $tc->name,
                'type' => $tc->type,
                'weight' => (float) $tc->weight,
                'timeout_ms' => $tc->timeout_ms,
                'is_required' => $tc->is_required,
                'script' => $tc->script,
            ])->toArray(),
            'limits' => [
                'total_timeout_ms' => (int) config('services.runner.total_timeout', 10000),
                'memory_mb' => 128,
                'cpu_ms' => 5000,
            ],
        ];

        try {
            $response = Http::timeout($this->timeout / 1000)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/api/v1/evaluate", $payload);

            if ($response->failed()) {
                Log::error('Runner request failed', [
                    'submission_id' => $submission->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new RunnerException(
                    "Runner error: {$response->status()}",
                    $response->status(),
                    $response->json() ?? []
                );
            }

            return $response->json();
        } catch (\Illuminate\Http\Exceptions\RequestException $e) {
            Log::error('Runner connection error', [
                'submission_id' => $submission->id,
                'error' => $e->getMessage(),
            ]);
            throw new RunnerException(
                'Cannot connect to runner: ' . $e->getMessage(),
                0,
                []
            );
        }
    }

    public function getJobStatus(string $runnerJobId): ?array
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ])
                ->get("{$this->baseUrl}/api/v1/jobs/{$runnerJobId}");

            if ($response->notFound()) {
                return null;
            }

            return $response->json();
        } catch (\Illuminate\Http\Exceptions\RequestException $e) {
            return null;
        }
    }
}

class RunnerException extends \Exception
{
    public array $details;

    public function __construct(string $message, int $code, array $details)
    {
        parent::__construct($message, $code);
        $this->details = $details;
    }
}