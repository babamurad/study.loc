<?php

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Models\UserLessonProgress;
use App\Services\LessonAccessService;
use App\Livewire\CourseShow;
use App\Livewire\LessonShow;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/courses/{course}', CourseShow::class)->name('courses.show');

    Route::get('/courses/{course}/lessons/{lesson}', LessonShow::class)->name('lessons.show');

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