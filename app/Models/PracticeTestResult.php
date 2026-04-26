<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PracticeTestResult extends Model
{
    use HasFactory;

    protected $table = 'practice_test_results';

    protected $fillable = [
        'practice_submission_id',
        'practice_test_case_id',
        'passed',
        'earned_weight',
        'duration_ms',
        'message',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'passed' => 'boolean',
            'earned_weight' => 'decimal:1',
            'duration_ms' => 'integer',
            'meta' => 'array',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(PracticeSubmission::class, 'practice_submission_id');
    }

    public function testCase(): BelongsTo
    {
        return $this->belongsTo(PracticeTestCase::class, 'practice_test_case_id');
    }
}