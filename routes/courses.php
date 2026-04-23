<?php

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Models\UserLessonProgress;
use App\Services\LessonAccessService;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/courses/{course}', function (Course $course) {
        $course->load(['modules.lessons' => fn ($q) => $q->orderBy('position')]);
        return view('livewire.course-show', ['course' => $course]);
    })->name('courses.show');

    Route::get('/courses/{course}/lessons/{lesson}', function (Course $course, Lesson $lesson) {
        $accessService = app(LessonAccessService::class);

        abort_unless($accessService->canAccess(auth()->user(), $lesson), 403);

        return view('livewire.lesson-show', [
            'course' => $course,
            'lesson' => $lesson,
        ]);
    })->name('lessons.show');

    Route::post('/lessons/{lesson}/complete', function (Lesson $lesson) {
        $accessService = app(LessonAccessService::class);

        abort_unless($accessService->canAccess(auth()->user(), $lesson), 403);

        UserLessonProgress::updateOrCreate(
            ['user_id' => auth()->id(), 'lesson_id' => $lesson->id],
            ['status' => 'completed', 'completed_at' => now()]
        );

        $nextLesson = Lesson::where('course_id', $lesson->course_id)
            ->where('position', $lesson->position + 1)
            ->first();

        if ($nextLesson) {
            return redirect()->route('lessons.show', [
                'course' => $lesson->course_id,
                'lesson' => $nextLesson->id,
            ]);
        }

        return redirect()->route('courses.show', ['course' => $lesson->course_id])
            ->with('success', 'Курс успешно завершён!');
    })->name('lessons.complete');
});