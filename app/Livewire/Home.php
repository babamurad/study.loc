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
    public function render(LessonAccessService $lessonAccessService): View
    {
        /** @var Course|null $course */
        $course = Course::query()->with(['modules.lessons' => fn($q) => $q->orderBy('position')])->first();
        
        $currentLesson = null;
        $user = Auth::user();
        
        if ($course) {
            if ($user) {
                $currentLesson = $lessonAccessService->getFirstAvailableLesson($user, $course);
            } else {
                $currentLesson = $course->lessons()->orderBy('position')->first();
            }
        }
        
        return view('livewire.home', [
            'course' => $course,
            'currentLesson' => $currentLesson,
            'user' => $user,
            'lessonAccessService' => $lessonAccessService,
        ]);
    }
}
