<?php

namespace App\Services;

use App\Models\Practice;
use App\Models\PracticeSubmission;
use App\Models\PracticeTestResult;

class SubmissionScoringService
{
    public function calculate(PracticeSubmission $submission): array
    {
        $practice = $submission->practice;
        $results = $submission->testResults;
        $testCases = $practice->testCases;

        $maxWeight = $testCases->sum('weight');
        $earnedWeight = $results->where('passed', true)->sum('earned_weight');

        $score = $maxWeight > 0
            ? round(($earnedWeight / $maxWeight) * 10, 1)
            : 0.0;

        $hasFailedRequired = $results
            ->whereHas('testCase', fn($q) => $q->where('is_required', true))
            ->where('passed', false)
            ->isNotEmpty();

        $passed = $hasFailedRequired
            ? false
            : $score >= (float) $practice->pass_score;

        return [
            'score' => $score,
            'passed' => $passed,
            'max_weight' => $maxWeight,
            'earned_weight' => $earnedWeight,
            'has_failed_required' => $hasFailedRequired,
        ];
    }

    public function processResults(PracticeSubmission $submission, array $runnerResults): void
    {
        $testResults = $submission->testResults()->get()->keyBy('practice_test_case_id');
        $passed = 0;
        $earned = 0.0;

        foreach ($runnerResults['tests'] ?? [] as $testResult) {
            $testCaseId = $testResult['id'] ?? null;
            $resultModel = $testResults->get($testCaseId);

            if (!$resultModel) {
                continue;
            }

            $resultModel->passed = $testResult['passed'] ?? false;
            $resultModel->earned_weight = $testResult['passed'] ? (float) ($testResult['earned_score'] ?? $resultModel->testCase->weight) : 0.0;
            $resultModel->duration_ms = $testResult['duration_ms'] ?? 0;
            $resultModel->message = $testResult['message'] ?? null;
            $resultModel->meta = $testResult['meta'] ?? null;
            $resultModel->save();

            if ($testResult['passed']) {
                $passed++;
                $earned += $resultModel->earned_weight;
            }
        }

        $scoring = $this->calculate($submission);
        $submission->score = $scoring['score'];
        $submission->passed = $scoring['passed'];
        $submission->status = PracticeSubmission::STATUS_COMPLETED;
        $submission->checked_at = now();
        $submission->raw_result = $runnerResults;
        $submission->save();
    }
}