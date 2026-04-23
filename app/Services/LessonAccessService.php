<?php

namespace App\Services;

use App\Models\Lesson;
use App\Models\User;
use App\Models\UserLessonProgress;

class LessonAccessService
{
    public function canAccess(User $user, Lesson $lesson): bool
    {
        if ($lesson->position === 1) {
            return true;
        }

        $previousLesson = Lesson::where('course_id', $lesson->course_id)
            ->where('position', $lesson->position - 1)
            ->first();

        if (!$previousLesson) {
            return true;
        }

        return UserLessonProgress::where('user_id', $user->id)
            ->where('lesson_id', $previousLesson->id)
            ->exists();
    }

    public function getStatus(User $user, Lesson $lesson): string
    {
        if ($lesson->isCompletedBy($user)) {
            return 'completed';
        }

        if ($this->canAccess($user, $lesson)) {
            return 'available';
        }

        return 'locked';
    }

    public function getProgressPercent(User $user, int $courseId): int
    {
        $totalLessons = Lesson::where('course_id', $courseId)->count();

        if ($totalLessons === 0) {
            return 0;
        }

        $completedLessons = UserLessonProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', Lesson::where('course_id', $courseId)->pluck('id'))
            ->count();

        return (int) round($completedLessons / $totalLessons * 100);
    }
}