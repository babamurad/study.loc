<?php

use App\Models\LessonPractice;
use App\Models\LessonPracticeFactory;
use App\Models\PracticeTestCase;
use App\Models\PracticeTestCaseFactory;
use App\Models\PracticeSubmission;
use App\Models\PracticeSubmissionFactory;
use App\Models\PracticeTestResult;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->practice = LessonPractice::factory()->create();
    $this->testCases = PracticeTestCase::factory()->count(3)->create([
        'lesson_practice_id' => $this->practice->id,
    ]);
});

describe('LessonPractice model', function () {
    test('has test cases', function () {
        expect($this->practice->testCases)->toHaveCount(3);
    });

    test('calculates max weight', function () {
        expect($this->practice->max_weight)->toBeGreaterThan(0);
    });
});

describe('PracticeSubmission scoring', function () {
    test('score is 0 when no tests passed', function () {
        $submission = PracticeSubmission::factory()->create([
            'user_id' => $this->user->id,
            'lesson_practice_id' => $this->practice->id,
            'status' => PracticeSubmission::STATUS_COMPLETED,
        ]);

        foreach ($this->testCases as $testCase) {
            PracticeTestResult::create([
                'practice_submission_id' => $submission->id,
                'practice_test_case_id' => $testCase->id,
                'passed' => false,
                'earned_weight' => 0,
            ]);
        }

        $submission->refresh();
        expect($submission->score)->toBeNull();
    });

    test('submission calculates next attempt number', function () {
        $attempt1 = PracticeSubmission::factory()->create([
            'user_id' => $this->user->id,
            'lesson_practice_id' => $this->practice->id,
            'attempt_no' => 1,
        ]);

        $nextAttempt = PracticeSubmission::getNextAttemptNumber(
            $this->user->id, 
            $this->practice->id
        );

        expect($nextAttempt)->toBe(2);
    });
});

describe('Required tests', function () {
    test('submission fails if required test fails', function () {
        $requiredTestCase = PracticeTestCase::factory()->create([
            'lesson_practice_id' => $this->practice->id,
            'is_required' => true,
            'weight' => 5,
        ]);

        $optionalTestCase = PracticeTestCase::factory()->create([
            'lesson_practice_id' => $this->practice->id,
            'is_required' => false,
            'weight' => 5,
        ]);

        $submission = PracticeSubmission::factory()->create([
            'user_id' => $this->user->id,
            'lesson_practice_id' => $this->practice->id,
        ]);

        PracticeTestResult::create([
            'practice_submission_id' => $submission->id,
            'practice_test_case_id' => $requiredTestCase->id,
            'passed' => false,
            'earned_weight' => 0,
        ]);

        PracticeTestResult::create([
            'practice_submission_id' => $submission->id,
            'practice_test_case_id' => $optionalTestCase->id,
            'passed' => true,
            'earned_weight' => 5,
        ]);

        expect($submission->hasFailedRequiredTest())->toBe(true);
    });
});