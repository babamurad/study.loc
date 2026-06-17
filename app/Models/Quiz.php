<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'pass_threshold',
        'time_limit',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function course(): HasOneThrough
    {
        return $this->hasOneThrough(
            Course::class,
            Lesson::class,
            'quiz_id', // Foreign key on the lessons table
            'id',      // Foreign key on the courses table
            'id',      // Local key on the quizzes table
            'course_id' // Local key on the lessons table
        );
    }
}
