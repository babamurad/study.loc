<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Course;
use App\Models\User;
use App\Services\LessonAccessService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public ?User $student = null;

    public function mount(?User $student = null)
    {
        if ($student && $student->id) {
            if (!Auth::user()->isTeacher()) {
                abort(403);
            }
            $this->student = $student;
        } elseif (Auth::user()->isTeacher()) {
            return redirect()->route('teacher.dashboard');
        }
    }

    public function render(LessonAccessService $accessService)
    {
        /** @var User $user */
        $user = $this->student ?? Auth::user();

        $courses = Course::where('is_published', true)->get();

        $courseProgress = [];

        foreach ($courses as $course) {
            $totalLessons = $course->lessons()->count();
            if ($totalLessons === 0) {
                continue; // Skip empty courses
            }

            $percentage = $accessService->getProgressPercent($user, $course->id);
            $nextLesson = $accessService->getFirstAvailableLesson($user, $course);

            $completedLessons = $user->completedLessons()
                ->wherePivot('status', 'completed')
                ->whereIn('lesson_id', $course->lessons()->pluck('id'))
                ->count();

            $courseProgress[] = [
                'course' => $course,
                'total_lessons' => $totalLessons,
                'completed_lessons' => $completedLessons,
                'percentage' => $percentage,
                'next_lesson' => $nextLesson,
            ];
        }

        return view('livewire.dashboard', [
            'courseProgress' => collect($courseProgress),
            'user' => $user,
        ]);
    }
}
