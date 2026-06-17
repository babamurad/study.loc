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
    public string $statusFilter = 'all';
    public string $sortOrder = 'progress_desc';

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

            // Apply filter
            if ($this->statusFilter === 'completed' && $percentage < 100) {
                continue;
            }
            if ($this->statusFilter === 'not_started' && $percentage > 0) {
                continue;
            }
            if ($this->statusFilter === 'in_progress' && ($percentage === 0 || $percentage === 100)) {
                continue;
            }

            $courseProgress[] = [
                'course' => $course,
                'total_lessons' => $totalLessons,
                'completed_lessons' => $completedLessons,
                'percentage' => $percentage,
                'next_lesson' => $nextLesson,
            ];
        }

        $courseProgress = collect($courseProgress);

        // Apply sorting
        if ($this->sortOrder === 'progress_desc') {
            $courseProgress = $courseProgress->sortByDesc('percentage');
        } elseif ($this->sortOrder === 'progress_asc') {
            $courseProgress = $courseProgress->sortBy('percentage');
        } elseif ($this->sortOrder === 'title_asc') {
            $courseProgress = $courseProgress->sortBy(fn($item) => $item['course']->title);
        } elseif ($this->sortOrder === 'title_desc') {
            $courseProgress = $courseProgress->sortByDesc(fn($item) => $item['course']->title);
        }

        return view('livewire.dashboard', [
            'courseProgress' => $courseProgress->values(),
            'user' => $user,
        ]);
    }
}
