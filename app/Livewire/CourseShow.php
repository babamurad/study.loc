<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\Lesson;
use App\Services\LessonAccessService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.front')]
class CourseShow extends Component
{
    public Course $course;
    public array $lessonStatuses = [];

    public function mount(Course $course)
    {
        $this->course = $course->load([
            'modules.lessons' => fn ($q) => $q->orderBy('position'),
            'modules.practices' => fn ($q) => $q->where('is_active', true)
        ]);
        $this->loadLessonStatuses();
    }

    public function loadLessonStatuses(): void
    {
        $user = auth()->user();
        $accessService = app(LessonAccessService::class);

        $this->lessonStatuses = [];
        foreach ($this->course->lessons as $lesson) {
            $this->lessonStatuses[$lesson->id] = $accessService->getStatus($user, $lesson);
        }
    }

    public function getProgressPercentProperty(): int
    {
        return app(LessonAccessService::class)->getProgressPercent(auth()->user(), $this->course->id);
    }

    public function render()
    {
        return view('livewire.course-show');
    }
}