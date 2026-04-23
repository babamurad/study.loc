<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'module_id',
        'title',
        'slug',
        'content',
        'position',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'position' => 'integer',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(UserLessonProgress::class);
    }

    public function quiz(): HasOne
    {
        return $this->hasOne(LessonQuiz::class);
    }

    public function isCompletedBy(User $user): bool
    {
        return $this->progress()->where('user_id', $user->id)->where('status', 'completed')->exists();
    }

    public function previousLesson(): ?Lesson
    {
        return self::where('course_id', $this->course_id)
            ->where('position', '<', $this->position)
            ->orderBy('position', 'desc')
            ->first();
    }

    public function nextLesson(): ?Lesson
    {
        return self::where('course_id', $this->course_id)
            ->where('position', '>', $this->position)
            ->orderBy('position', 'asc')
            ->first();
    }
}