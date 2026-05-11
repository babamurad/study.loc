<?php

namespace App\Services;

use App\Contracts\PracticeEvaluatorInterface;
use App\Models\PracticeSubmission;
use App\Models\PracticeTestCase;
use App\Services\Evaluators\GeminiEvaluator;
use App\Services\Evaluators\LocalPhpEvaluator;
use App\Services\Evaluators\NodeRunnerEvaluator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RunnerClient
{
    private string $baseUrl;
    private string $apiKey;
    private int $timeout;

    private bool $mock;

    private PracticeEvaluatorInterface $evaluator;

    public function __construct()
    {
        $this->baseUrl = config('services.runner.url', 'http://localhost:8080');
        $this->apiKey = config('services.runner.api_key', 'test-key');
        $this->timeout = config('services.runner.timeout', 5000);
        $this->mock = config('services.runner.mock', true);

        $this->evaluator = $this->resolveEvaluator();
    }

    private function resolveEvaluator(): PracticeEvaluatorInterface
    {
        if (!$this->mock) {
            return new NodeRunnerEvaluator();
        }

        $driver = config('services.practice_evaluator.driver', 'local');

        return match ($driver) {
            'gemini' => new GeminiEvaluator(),
            'node' => new NodeRunnerEvaluator(),
            default => new LocalPhpEvaluator(),
        };
    }

    public function evaluate(PracticeSubmission $submission): array
    {
        return $this->evaluator->evaluate($submission);
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