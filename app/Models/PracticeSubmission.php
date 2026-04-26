<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PracticeSubmission extends Model
{
    use HasFactory;

    protected $table = 'practice_submissions';

    protected $fillable = [
        'user_id',
        'lesson_practice_id',
        'html_code',
        'css_code',
        'js_code',
        'status',
        'score',
        'passed',
        'attempt_no',
        'runner_job_id',
        'runner_version',
        'started_at',
        'checked_at',
        'error_message',
        'raw_result',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:1',
            'passed' => 'boolean',
            'started_at' => 'datetime',
            'checked_at' => 'datetime',
            'raw_result' => 'array',
        ];
    }

    public const STATUS_PENDING = 'pending';
    public const STATUS_RUNNING = 'running';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_TIMEOUT = 'timeout';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_RUNNING,
        self::STATUS_COMPLETED,
        self::STATUS_FAILED,
        self::STATUS_TIMEOUT,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lessonPractice(): BelongsTo
    {
        return $this->belongsTo(LessonPractice::class, 'lesson_practice_id');
    }

    public function testResults(): HasMany
    {
        return $this->hasMany(PracticeTestResult::class, 'practice_submission_id');
    }

    public function isPassed(): bool
    {
        return $this->passed;
    }

    public function hasFailedRequiredTest(): bool
    {
        return $this->testResults()
            ->whereHas('testCase', fn($q) => $q->where('is_required', true))
            ->where('passed', false)
            ->exists();
    }

    public function scopePassed($query)
    {
        return $query->where('passed', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('passed', false);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_RUNNING]);
    }

    public static function getNextAttemptNumber(int $userId, int $lessonPracticeId): int
    {
        return self::where('user_id', $userId)
                ->where('lesson_practice_id', $lessonPracticeId)
                ->max('attempt_no') + 1;
    }
}