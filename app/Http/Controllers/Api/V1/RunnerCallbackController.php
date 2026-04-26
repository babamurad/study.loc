<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PracticeSubmission;
use App\Services\SubmissionScoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class RunnerCallbackController extends Controller
{
    public function __construct(
        private readonly SubmissionScoringService $scoringService
    ) {}

    public function handle(Request $request, string $submissionId): JsonResponse
    {
        $request->validate([
            'runner_job_id' => 'required|string',
            'status' => 'required|string|in:completed,failed,timeout',
            'tests' => 'array',
            'tests.*.id' => 'required|integer',
            'tests.*.passed' => 'required|boolean',
            'tests.*.earned_score' => 'nullable|numeric',
            'tests.*.duration_ms' => 'nullable|integer',
            'tests.*.message' => 'nullable|string',
        ]);

        $submission = PracticeSubmission::find($submissionId);

        if (!$submission) {
            return response()->json(['error' => 'Submission not found'], 404);
        }

        $hmacSignature = $request->header('X-Runner-Signature');
        if (!$this->verifySignature($request, $submission, $hmacSignature)) {
            Log::warning('Invalid runner callback signature', [
                'submission_id' => $submissionId,
                'ip' => $request->ip(),
            ]);
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $this->scoringService->processResults($submission, $request->all());

        Log::info('Runner callback processed', [
            'submission_id' => $submissionId,
            'status' => $request->status,
            'score' => $submission->fresh()->score,
        ]);

        return response()->json(['status' => 'ok']);
    }

    private function verifySignature(Request $request, PracticeSubmission $submission, ?string $signature): bool
    {
        $secret = config('services.runner.hmac_secret', 'default-secret');
        $timestamp = $request->header('X-Runner-Timestamp', 0);
        
        if (abs(time() - (int)$timestamp) > 300) {
            return false;
        }

        $payload = $timestamp . '.' . $submission->id . '.' . json_encode($request->all());
        $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature ?? '');
    }
}