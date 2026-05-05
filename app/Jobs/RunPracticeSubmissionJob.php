<?php

namespace App\Jobs;

use App\Models\PracticeSubmission;
use App\Models\PracticeTestCase;
use App\Models\PracticeTestResult;
use App\Services\RunnerClient;
use App\Services\SubmissionScoringService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RunPracticeSubmissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array|int $backoff = [1, 2, 4];
    public int $timeout = 30;

    public function __construct(
        public int $submissionId
    ) {}

    public function handle(RunnerClient $runnerClient, SubmissionScoringService $scoringService): void
    {
        $submission = PracticeSubmission::with(['practice.testCases', 'testResults'])->find($this->submissionId);

        if (!$submission) {
            Log::warning('Submission not found', ['submission_id' => $this->submissionId]);
            return;
        }

        if (!in_array($submission->status, [PracticeSubmission::STATUS_PENDING, PracticeSubmission::STATUS_RUNNING])) {
            Log::info('Submission already processed', [
                'submission_id' => $this->submissionId,
                'status' => $submission->status,
            ]);
            return;
        }

        $submission->status = PracticeSubmission::STATUS_RUNNING;
        $submission->started_at = now();
        $submission->save();

        try {
            $runnerResult = $runnerClient->evaluate($submission);

            $submission->runner_job_id = $runnerResult['runner_job_id'] ?? null;
            $submission->runner_version = $runnerResult['runner_version'] ?? null;
            $submission->raw_result = $runnerResult;
            $submission->save();

            if (($runnerResult['status'] ?? '') === 'completed') {
                $this->processTestResults($submission, $runnerResult);
            } else {
                $this->dispatchSelfForCheck($submission);
            }
        } catch (\App\Services\RunnerException $e) {
            $this->handleRunnerError($submission, $e);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $submission = PracticeSubmission::find($this->submissionId);

        if (!$submission) {
            return;
        }

        $submission->status = PracticeSubmission::STATUS_FAILED;
        $submission->error_message = $exception->getMessage();
        $submission->save();

        Log::error('Practice submission failed permanently', [
            'submission_id' => $this->submissionId,
            'error' => $exception->getMessage(),
        ]);
    }

    private function processTestResults(PracticeSubmission $submission, array $runnerResult): void
    {
        $scoringService = app(SubmissionScoringService::class);
        $scoringService->processResults($submission, $runnerResult);

        if ($submission->passed) {
            $this->updateLessonProgress($submission);
        }
    }

    private function updateLessonProgress(PracticeSubmission $submission): void
    {
        $practice = $submission->practice;
        $user = $submission->user;
        
        if ($practice->practicable_type !== \App\Models\Lesson::class) {
            return;
        }
        
        $lesson = $practice->practicable;

        $progress = $lesson->progress()->where('user_id', $user->id)->first();

        $allPracticesPassed = \App\Models\Practice::where('practicable_type', \App\Models\Lesson::class)
            ->where('practicable_id', $lesson->id)
            ->where('is_active', true)
            ->whereHas('submissions', fn($q) => $q->where('user_id', $user->id)->where('passed', true))
            ->exists();

        $requiredPracticesPassed = !\App\Models\Practice::where('practicable_type', \App\Models\Lesson::class)
            ->where('practicable_id', $lesson->id)
            ->where('is_active', true)
            ->whereDoesntHave('submissions', fn($q) => $q->where('user_id', $user->id)->where('passed', true))
            ->exists();

        if ($progress && $allPracticesPassed) {
            $progress->status = 'completed';
            $progress->completed_at = now();
            $progress->save();
        }
    }

    private function handleRunnerError(PracticeSubmission $submission, \App\Services\RunnerException $e): void
    {
        $submission->status = match ($e->getCode()) {
            0, 503 => PracticeSubmission::STATUS_FAILED,
            504 => PracticeSubmission::STATUS_TIMEOUT,
            default => PracticeSubmission::STATUS_FAILED,
        };
        $submission->error_message = $e->getMessage();
        $submission->save();
    }

    private function dispatchSelfForCheck(PracticeSubmission $submission): void
    {
        if ($submission->runner_job_id) {
            self::dispatch($submission->id)
                ->delay(now()->addSeconds(2))
                ->onQueue('practice-checks');
        }
    }
}