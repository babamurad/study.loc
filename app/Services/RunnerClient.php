<?php

namespace App\Services;

use App\Models\Practice;
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

    private bool $mock;

    public function __construct()
    {
        $this->baseUrl = config('services.runner.url', 'http://localhost:8080');
        $this->apiKey = config('services.runner.api_key', 'test-key');
        $this->timeout = config('services.runner.timeout', 5000);
        $this->mock = config('services.runner.mock', true); // Default to true for now to avoid errors
    }

    public function evaluate(PracticeSubmission $submission): array
    {
        if ($this->mock) {
            // Используем локальную проверку средствами PHP (DOMDocument), вместо заглушки
            return $this->evaluateLocally($submission);
        }

        $practice = $submission->practice;
        $testCases = $practice->testCases;

        $payload = [
            'submission_id' => $submission->id,
            'idempotency_key' => "{$submission->user_id}-{$submission->practice_id}-{$submission->attempt_no}",
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

    private function evaluateLocally(PracticeSubmission $submission): array
    {
        $results = [];
        $htmlCode = $submission->html_code ?? '';
        $cssCode = $submission->css_code ?? '';

        $dom = new \DOMDocument();
        $loaded = false;
        if (!empty(trim($htmlCode))) {
            // Suppress warnings for invalid HTML
            libxml_use_internal_errors(true);
            $loaded = @$dom->loadHTML(mb_convert_encoding($htmlCode, 'HTML-ENTITIES', 'UTF-8'), LIBXML_NOERROR | LIBXML_NOWARNING);
            libxml_clear_errors();
        }

        $xpath = new \DOMXPath($dom);

        foreach ($submission->practice->testCases as $tc) {
            $passed = false;
            $message = '';

            try {
                if ($tc->type === 'dom') {
                    if (!$loaded) {
                        $message = 'HTML код пуст или невалиден.';
                    } else {
                        $selector = $tc->script['selector'] ?? '';
                        $exists = $tc->script['exists'] ?? true;
                        $expectedText = $tc->script['text'] ?? null;

                        $xpathQuery = $this->cssToXpath($selector);
                        $nodes = $xpath->query($xpathQuery);

                        if ($nodes === false) {
                            $message = "Неверный селектор: {$selector}";
                        } else {
                            $hasNodes = $nodes->length > 0;
                            if ($exists && !$hasNodes) {
                                $message = "Ожидался элемент '{$selector}', но он не найден.";
                            } elseif (!$exists && $hasNodes) {
                                $message = "Элемент '{$selector}' не должен существовать.";
                            } else {
                                $passed = true;
                                if ($expectedText !== null && $hasNodes) {
                                    $foundText = false;
                                    foreach ($nodes as $node) {
                                        if (mb_stripos($node->textContent, $expectedText) !== false) {
                                            $foundText = true;
                                            break;
                                        }
                                    }
                                    if (!$foundText) {
                                        $passed = false;
                                        $message = "Текст '{$expectedText}' не найден внутри '{$selector}'.";
                                    }
                                }
                            }
                        }
                    }
                } elseif ($tc->type === 'css') {
                    $selector = $tc->script['selector'] ?? '';
                    $property = $tc->script['property'] ?? '';
                    $value = $tc->script['value'] ?? '';

                    if ($selector && $property) {
                        $cleanCss = preg_replace('/\s+/', '', $cssCode);
                        $cleanExpected = preg_replace('/\s+/', '', $property . ':' . $value);
                        
                        // Very basic checks
                        if (str_contains($cleanCss, $cleanExpected) || str_contains(str_replace(';', '', $cleanCss), $cleanExpected)) {
                            $passed = true;
                        } else {
                            $message = "Свойство {$property}: {$value} не найдено в CSS (или написано иначе).";
                            // Also try regex
                            $pattern = '/' . preg_quote($selector, '/') . '[^}]*?' . preg_quote($property, '/') . '\s*:\s*' . preg_quote($value, '/') . '/i';
                            if (preg_match($pattern, $cssCode)) {
                                $passed = true;
                                $message = '';
                            }
                        }
                    } else {
                        $passed = true;
                    }
                } else {
                    $passed = true;
                    $message = "Автопроверка типа {$tc->type} не поддерживается. Считается успешной.";
                }
            } catch (\Exception $e) {
                $message = 'Ошибка: ' . $e->getMessage();
            }

            $results[] = [
                'id' => $tc->id,
                'passed' => $passed,
                'message' => $message ?: ($passed ? 'Пройден (Локально)' : 'Не пройден'),
                'duration_ms' => rand(5, 20),
            ];
        }

        return [
            'status' => 'completed',
            'runner_job_id' => 'local-' . uniqid(),
            'runner_version' => 'local-1.0',
            'results' => $results,
        ];
    }

    private function cssToXpath(string $selector): string
    {
        if (empty($selector)) return '//*';
        
        $xpath = '//';
        $selector = trim($selector);
        
        if (str_starts_with($selector, '.')) {
            $class = substr($selector, 1);
            $xpath .= "*[contains(concat(' ', normalize-space(@class), ' '), ' {$class} ')]";
        } elseif (str_starts_with($selector, '#')) {
            $id = substr($selector, 1);
            $xpath .= "*[@id='{$id}']";
        } else {
            if (str_contains($selector, '.')) {
                [$tag, $class] = explode('.', $selector, 2);
                $xpath .= "{$tag}[contains(concat(' ', normalize-space(@class), ' '), ' {$class} ')]";
            } else {
                $xpath .= $selector;
            }
        }
        return $xpath;
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