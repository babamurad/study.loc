<?php

declare(strict_types=1);

namespace App\Livewire\Teacher\Students;

use App\Models\PracticeSubmission;
use App\Models\User;
use App\Models\UserQuizAttempt;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Progress extends Component
{
    public User $student;

    public function mount(User $student)
    {
        $this->student = $student;
    }

    public function render()
    {
        $quizAttempts = UserQuizAttempt::where('user_id', $this->student->id)
            ->with('quiz.course')
            ->orderByDesc('created_at')
            ->get();

        $practiceSubmissions = PracticeSubmission::where('user_id', $this->student->id)
            ->with('practice.lesson.course')
            ->orderByDesc('created_at')
            ->get();

        return view('livewire.teacher.students.progress', [
            'quizAttempts' => $quizAttempts,
            'practiceSubmissions' => $practiceSubmissions,
        ]);
    }
}
