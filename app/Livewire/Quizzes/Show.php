<?php

namespace App\Livewire\Quizzes;

use App\Models\Quiz;
use App\Models\UserQuizAttempt;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Show extends Component
{
    public Quiz $quiz;
    public $questions;
    public array $userAnswers = [];
    public int $currentQuestionIndex = 0;
    public bool $quizInProgress = false;
    public ?array $quizResult = null;

    public function mount(Quiz $quiz)
    {
        $this->quiz = $quiz->load('questions.answers');
        $this->questions = $this->quiz->questions;

        $latestAttempt = UserQuizAttempt::where('user_id', auth()->id())
            ->where('quiz_id', $this->quiz->id)
            ->latest()
            ->first();

        if ($latestAttempt) {
            $this->quizResult = [
                'score' => $latestAttempt->score,
                'passed' => $latestAttempt->passed,
            ];
        }
    }

    public function startQuiz()
    {
        $this->quizInProgress = true;
        $this->currentQuestionIndex = 0;
        $this->userAnswers = [];
        $this->quizResult = null;
    }

    public function nextQuestion()
    {
        if ($this->currentQuestionIndex < $this->questions->count() - 1) {
            $this->currentQuestionIndex++;
        }
    }

    public function previousQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        }
    }

    public function submitQuiz()
    {
        $correctAnswers = 0;
        $totalQuestions = $this->questions->count();

        foreach ($this->questions as $question) {
            $userAnswerId = $this->userAnswers[$question->id] ?? null;
            
            \Illuminate\Support\Facades\Log::info('Checking answer', [
                'question_id' => $question->id,
                'user_answer_id' => $userAnswerId,
                'userAnswers_array' => $this->userAnswers
            ]);

            if ($userAnswerId) {
                // Query the database directly to avoid any collection hydration or strict type issues
                $isCorrect = \App\Models\QuizAnswer::where('id', $userAnswerId)
                    ->where('is_correct', true)
                    ->exists();

                if ($isCorrect) {
                    $correctAnswers++;
                }
            }
        }

        $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;
        $passed = $score >= $this->quiz->pass_threshold;

        UserQuizAttempt::create([
            'user_id' => auth()->id(),
            'quiz_id' => $this->quiz->id,
            'score' => $score,
            'passed' => $passed,
        ]);

        $this->quizResult = [
            'score' => $score,
            'passed' => $passed,
        ];
        $this->quizInProgress = false;
    }

    public function retakeQuiz()
    {
        $this->startQuiz();
    }

    public function render()
    {
        return view('livewire.quizzes.show');
    }
}
