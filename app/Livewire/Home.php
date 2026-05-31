<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Course;
use App\Models\Lesson;
use App\Services\LessonAccessService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.front')]
class Home extends Component
{
    public ?int $selectedCourseId = null;

    public function mount(): void
    {
        $first = Course::query()->orderBy('id')->first();
        $this->selectedCourseId = $first?->id;
    }

    public function selectCourse(int $courseId): void
    {
        $this->selectedCourseId = $courseId;
    }

    public function render(LessonAccessService $lessonAccessService): View
    {
        $courses = Course::query()->with(['modules.lessons' => fn($q) => $q->orderBy('position')])->get();

        $selectedCourse = $courses->firstWhere('id', $this->selectedCourseId);

        $currentLesson = null;
        $user = Auth::user();

        if ($user) {
            foreach ($courses as $course) {
                $currentLesson = $lessonAccessService->getFirstAvailableLesson($user, $course);
                if ($currentLesson) {
                    break;
                }
            }
        } elseif ($courses->isNotEmpty()) {
            $currentLesson = $courses->first()->lessons()->orderBy('position')->first();
        }

        return view('livewire.home', [
            'courses' => $courses,
            'selectedCourse' => $selectedCourse,
            'currentLesson' => $currentLesson,
            'user' => $user,
            'lessonAccessService' => $lessonAccessService,
        ]);
    }
}
