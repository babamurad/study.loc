<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Practice;
use App\Models\Quiz;
use App\Models\PracticeSubmission;
use App\Models\QuizQuestion;
use App\Models\User;
use App\Models\UserQuizAttempt;
use App\Services\LessonAccessService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Illuminate\Database\Eloquent\Collection;

#[Layout('layouts.front')]
class LessonShow extends Component
{
    public Course $course;
    public Lesson $lesson;
    public bool $justCompleted = false;

    // Quiz Properties
    public ?Quiz $quiz = null;
    public Collection $questions;
    public array $userAnswers = [];
    public int $currentQuestionIndex = 0;
    public ?array $quizResult = null;
    public bool $quizInProgress = false;

    // Practice Properties
    public ?Practice $practice = null;
    public ?Practice $modulePractice = null;
    public bool $practiceExpanded = false;

    public function mount(Course $course, Lesson $lesson, LessonAccessService $accessService, ?int $module_practice = null): void
    {
        \Illuminate\Support\Facades\Log::info('LessonShow mounting', ['lesson_id' => $lesson->id]);
        $this->course = $course;
        $this->lesson = $lesson->load('module.course', 'quiz.questions.answers', 'practice.testCases'); 

        /** @var User|null $user */
        $user = Auth::user();

        \Illuminate\Support\Facades\Log::info('LessonShow user check', ['user_id' => $user?->id]);

        if (!$user || !$accessService->canAccess($user, $this->lesson)) {
            abort(403, 'У вас нет доступа к этому уроку.');
        }

        $this->quiz = $this->lesson->quiz;
        $this->practice = $this->lesson->practice;

        if ($this->practice) {
            $this->practice = $this->practice->load(['testCases' => fn($q) => $q->orderBy('sort_order')]);
        }

        if ($module_practice) {
            $this->modulePractice = Practice::where('id', $module_practice)
                ->where('is_active', true)
                ->with(['testCases' => fn($q) => $q->orderBy('sort_order')])
                ->first();
        }
    }

    public function togglePractice(): void
    {
        $this->practiceExpanded = !$this->practiceExpanded;
    }


    public function complete(LessonAccessService $accessService): void
    {
        /** @var User $user */
        $user = Auth::user();

        if ($this->quiz) {
            $latestAttempt = UserQuizAttempt::where('user_id', $user->id)
                ->where('quiz_id', $this->quiz->id)
                ->latest()
                ->first();

            if (!$latestAttempt || !$latestAttempt->passed) {
                session()->flash('error', 'Вы должны сначала пройти тест, чтобы завершить урок.');
                return;
            }
        }


        if ($accessService->completeLesson($user, $this->lesson)) {
            $this->justCompleted = true;
            $this->dispatch('lesson-completed');
        } else {
             session()->flash('error', 'Не удалось завершить урок.');
        }
    }

    #[Computed]
    public function nextLesson(): ?Lesson
    {
        return Lesson::query()
            ->where('course_id', $this->course->id)
            ->where('position', '>', $this->lesson->position)
            ->orderBy('position')
            ->first();
    }

    #[Computed]
    public function progressPercent(): int
    {
        /** @var User $user */
        $user = Auth::user();
        return app(LessonAccessService::class)->getProgressPercent($user, $this->course->id);
    }

    #[Computed]
    public function canCompleteLesson(): bool
    {
        /** @var User $user */
        $user = Auth::user();

        if ($this->quiz) {
            $quizPassed = UserQuizAttempt::where('user_id', $user->id)
                ->where('quiz_id', $this->quiz->id)
                ->where('passed', true)
                ->exists();
            if (!$quizPassed) {
                return false;
            }
        }


        return true;
    }

    public function render(): View
    {
        return view('livewire.lesson-show');
    }
}
