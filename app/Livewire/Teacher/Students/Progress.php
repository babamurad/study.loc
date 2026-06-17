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

    public string $quizFilter = 'all'; // all, passed, failed
    public string $quizSort = 'date_desc'; // date_desc, date_asc, score_desc

    public string $practiceFilter = 'all'; // all, passed, failed
    public string $practiceSort = 'date_desc'; // date_desc, date_asc

    public function mount(User $student)
    {
        $this->student = $student;
    }

    public function render()
    {
        $quizQuery = UserQuizAttempt::where('user_id', $this->student->id)
            ->with('quiz.course');

        if ($this->quizFilter === 'passed') {
            $quizQuery->where('passed', true);
        } elseif ($this->quizFilter === 'failed') {
            $quizQuery->where('passed', false);
        }

        if ($this->quizSort === 'date_desc') {
            $quizQuery->orderByDesc('created_at');
        } elseif ($this->quizSort === 'date_asc') {
            $quizQuery->orderBy('created_at');
        } elseif ($this->quizSort === 'score_desc') {
            $quizQuery->orderByDesc('score');
        }

        $quizAttempts = $quizQuery->get();

        $practiceQuery = PracticeSubmission::where('user_id', $this->student->id)
            ->with('practice.practicable');

        if ($this->practiceFilter === 'passed') {
            $practiceQuery->where('status', 'completed')->where('passed', true);
        } elseif ($this->practiceFilter === 'failed') {
            $practiceQuery->where(function ($q) {
                $q->where('status', 'failed')
                  ->orWhere(function ($q) {
                      $q->where('status', 'completed')->where('passed', false);
                  });
            });
        }

        if ($this->practiceSort === 'date_desc') {
            $practiceQuery->orderByDesc('created_at');
        } elseif ($this->practiceSort === 'date_asc') {
            $practiceQuery->orderBy('created_at');
        }

        $practiceSubmissions = $practiceQuery->get();

        return view('livewire.teacher.students.progress', [
            'quizAttempts' => $quizAttempts,
            'practiceSubmissions' => $practiceSubmissions,
        ]);
    }
}
