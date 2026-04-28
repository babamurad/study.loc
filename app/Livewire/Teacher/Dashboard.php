<?php

namespace App\Livewire\Teacher;

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\PracticeSubmission;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        $totalStudents = User::where('role', UserRole::Student)->count();
        $totalCourses = Course::count();
        $totalLessons = Lesson::count();

        $totalSubmissions = PracticeSubmission::count();
        $passedSubmissions = PracticeSubmission::where('passed', true)->count();
        $successRate = $totalSubmissions > 0 
            ? (int) round(($passedSubmissions / $totalSubmissions) * 100) 
            : 0;

        return view('livewire.teacher.dashboard', [
            'totalStudents' => $totalStudents,
            'totalCourses' => $totalCourses,
            'totalLessons' => $totalLessons,
            'successRate' => $successRate,
        ]);
    }
}

