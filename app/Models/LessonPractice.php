<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LessonPractice extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'title',
        'description',
        'runner_profile',
        'max_score',
        'pass_score',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'max_score' => 'decimal:1',
            'pass_score' => 'decimal:1',
            'sort_order' => 'integer',
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function testCases(): HasMany
    {
        return $this->hasMany(PracticeTestCase::class, 'lesson_practice_id')->orderBy('sort_order');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(PracticeSubmission::class, 'lesson_practice_id');
    }

    public function getMaxWeightAttribute(): float
    {
        return $this->testCases->sum('weight');
    }

    public function isPassedBy(User $user): bool
    {
        return $this->submissions()
            ->where('user_id', $user->id)
            ->where('passed', true)
            ->exists();
    }
}