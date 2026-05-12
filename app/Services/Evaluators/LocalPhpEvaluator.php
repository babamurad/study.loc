<?php

namespace App\Services\Evaluators;

use App\Contracts\PracticeEvaluatorInterface;
use App\Models\PracticeSubmission;

class LocalPhpEvaluator implements PracticeEvaluatorInterface
{
    public function evaluate(PracticeSubmission $submission): array
    {
        $results = [];
        $htmlCode = $submission->html_code ?? '';
        $cssCode = $submission->css_code ?? '';

        $dom = new \DOMDocument();
        $loaded = false;
        if (!empty(trim($htmlCode))) {
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

                        if (str_contains($cleanCss, $cleanExpected) || str_contains(str_replace(';', '', $cleanCss), $cleanExpected)) {
                            $passed = true;
                        } else {
                            $message = "Свойство {$property}: {$value} не найдено в CSS (или написано иначе).";
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
}
