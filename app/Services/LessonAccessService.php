<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Models\UserLessonProgress;
use App\Models\UserQuizAttempt;

final readonly class LessonAccessService
{
    public function canAccess(?User $user, Lesson $lesson): bool
    {
        if (!$user) {
            return false;
        }

        // The first lesson is always available.
        if ($lesson->position === 1) {
            return true;
        }

        $previousLesson = $lesson->previousLesson();

        if (!$previousLesson) {
            return true;
        }

        $previousLessonCompleted = UserLessonProgress::query()
            ->where('user_id', $user->id)
            ->where('lesson_id', $previousLesson->id)
            ->where('status', 'completed')
            ->exists();

        if (!$previousLessonCompleted) {
            return false;
        }

        $quiz = $previousLesson->quiz;
        if ($quiz) {
            return UserQuizAttempt::query()
                ->where('user_id', $user->id)
                ->where('lesson_quiz_id', $quiz->id)
                ->where('passed', true)
                ->exists();
        }

        return true;
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

    public function getFirstAvailableLesson(User $user, Course $course): ?Lesson
    {
        $lessons = $course->lessons;

        foreach ($lessons as $lesson) {
            if ($this->getStatus($user, $lesson) === 'available') {
                return $lesson;
            }
        }

        return $lessons->first();
    }

    public function getProgressPercent(User $user, int $courseId): int
    {
        $totalLessons = Lesson::query()->where('course_id', $courseId)->count();

        if ($totalLessons === 0) {
            return 0;
        }

        $completedLessons = UserLessonProgress::query()
            ->where('user_id', $user->id)
            ->whereIn('lesson_id', Lesson::query()->where('course_id', $courseId)->pluck('id'))
            ->where('status', 'completed')
            ->count();

        return (int) round(($completedLessons / $totalLessons) * 100);
    }

    public function completeLesson(User $user, Lesson $lesson): bool
    {
        if (!$this->canAccess($user, $lesson)) {
             // Re-check for the current lesson, as canAccess checks the *previous* one
            if ($lesson->position > 1) {
                $isCompleted = UserLessonProgress::query()
                    ->where('user_id', $user->id)
                    ->where('lesson_id', $lesson->id)
                    ->where('status', 'completed')
                    ->exists();
                if(!$isCompleted && !$this->canAccess($user, $lesson->nextLesson())) {
                     return false;
                }
            } else {
                 return false;
            }
        }

        $quiz = $lesson->quiz;
        if ($quiz) {
            $passedQuiz = UserQuizAttempt::query()
                ->where('user_id', $user->id)
                ->where('lesson_quiz_id', $quiz->id)
                ->where('passed', true)
                ->exists();

            if (!$passedQuiz) {
                return false;
            }
        }

        UserLessonProgress::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'status' => 'completed',
                'completed_at' => now(),
            ]
        );

        return true;
    }
}
