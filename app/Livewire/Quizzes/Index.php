<?php

namespace App\Livewire\Quizzes;

use App\Models\Quiz;
use App\Models\UserQuizAttempt;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public function render()
    {
        $quizzes = Quiz::withCount('questions')->orderBy('created_at', 'desc')->paginate(12);
        
        $attempts = UserQuizAttempt::where('user_id', auth()->id())
            ->whereIn('quiz_id', $quizzes->pluck('id'))
            ->get()
            ->groupBy('quiz_id');

        return view('livewire.quizzes.index', [
            'quizzes' => $quizzes,
            'attempts' => $attempts,
        ]);
    }
}
