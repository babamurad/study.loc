<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\Lesson;
use App\Services\LessonAccessService;
use Illuminate\Http\RedirectHandler;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.front')]
class LessonShow extends Component
{
    public Course $course;
    public Lesson $lesson;
    public bool $justCompleted = false;

    public function mount(Course $course, Lesson $lesson)
    {
        $this->course = $course;
        $this->lesson = $lesson;
    }

    public function complete()
    {
        $accessService = app(LessonAccessService::class);

        if (!$accessService->canAccess(auth()->user(), $this->lesson)) {
            return redirect()->route('courses.show', $this->course)->with('error', 'Урок недоступен');
        }

        $this->lesson->progress()->updateOrCreate(
            ['user_id' => auth()->id()],
            ['status' => 'completed', 'completed_at' => now()]
        );

        $this->justCompleted = true;
        $this->dispatch('lesson-completed');
    }

    public function getNextLessonProperty(): ?Lesson
    {
        return Lesson::where('course_id', $this->course->id)
            ->where('position', $this->lesson->position + 1)
            ->first();
    }

    public function getProgressPercentProperty(): int
    {
        return app(LessonAccessService::class)->getProgressPercent(auth()->user(), $this->course->id);
    }

    public function render()
    {
        return view('livewire.lesson-show');
    }
}