<?php

namespace App\Livewire;

use App\Jobs\RunPracticeSubmissionJob;
use App\Models\Practice;
use App\Models\PracticeSubmission;
use App\Models\PracticeTestCase;
use App\Models\PracticeTestResult;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PracticeEditor extends Component
{
    public Practice $practice;
    public string $htmlCode = '';
    public string $cssCode = '';
    public string $jsCode = '';

    public ?PracticeSubmission $currentSubmission = null;
    public ?PracticeSubmission $bestSubmission = null;
    public bool $isRunning = false;
    public bool $showResults = false;
    public array $testResults = [];
    public int $attemptCount = 0;
    public string $activeTab = 'html';

    protected $listeners = [
        'submitPractice' => 'submit',
        'retakePractice' => 'retake',
    ];

    public function mount(): void
    {
        $this->loadSubmissions();
        $this->loadLastCode();
    }

    public function loadSubmissions(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->currentSubmission = $this->practice->submissions()
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'running'])
            ->latest()
            ->first();

        $this->bestSubmission = $this->practice->submissions()
            ->where('user_id', $user->id)
            ->where('passed', true)
            ->orderByDesc('score')
            ->first();

        $this->attemptCount = $this->practice->submissions()
            ->where('user_id', $user->id)
            ->count();
    }

    public function loadLastCode(): void
    {
        $lastSubmission = $this->practice->submissions()
            ->where('user_id', Auth::id())
            ->latest()
            ->first();

        if ($lastSubmission) {
            $this->htmlCode = $lastSubmission->html_code ?? '';
            $this->cssCode = $lastSubmission->css_code ?? '';
            $this->jsCode = $lastSubmission->js_code ?? '';
        } else {
            $this->loadDefaultCode();
        }
    }

    public function loadDefaultCode(): void
    {
        if (empty($this->htmlCode)) {
            $this->htmlCode = '<div class="card">
  <h2>Заголовок карточки</h2>
  <p>Напишите здесь ваш контент...</p>
</div>';
        }
        if (empty($this->cssCode)) {
            $this->cssCode = "/* Ваш CSS код здесь */\n.card {\n  \n}";
        }
    }

    public function submit(): void
    {
        $this->validate([
            'htmlCode' => 'nullable|string|max:50000',
            'cssCode' => 'nullable|string|max:50000',
            'jsCode' => 'nullable|string|max:20000',
        ]);

        /** @var User $user */
        $user = Auth::user();

        $key = "practice-submit-{$user->id}-{$this->practice->id}";
        if (RateLimiter::tooManyAttempts($key, 6)) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Подождите before submitting again.']);
            return;
        }

        RateLimiter::hit($key, 10);

        $attemptNo = PracticeSubmission::getNextAttemptNumber($user->id, $this->practice->id);

        $submission = PracticeSubmission::create([
            'user_id' => $user->id,
            'practice_id' => $this->practice->id,
            'html_code' => $this->htmlCode,
            'css_code' => $this->cssCode,
            'js_code' => $this->jsCode,
            'status' => PracticeSubmission::STATUS_PENDING,
            'attempt_no' => $attemptNo,
        ]);

        $testCases = $this->practice->testCases;
        foreach ($testCases as $testCase) {
            PracticeTestResult::create([
                'practice_submission_id' => $submission->id,
                'practice_test_case_id' => $testCase->id,
                'passed' => false,
                'earned_weight' => 0.0,
                'duration_ms' => 0,
                'message' => null,
            ]);
        }

        $this->currentSubmission = $submission;
        $this->isRunning = true;
        $this->showResults = false;
        $this->attemptCount++;

        RunPracticeSubmissionJob::dispatch($submission->id)->onQueue('practice-checks');
    }

    public function retake(): void
    {
        $this->showResults = false;
        $this->testResults = [];
        $this->currentSubmission = null;
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function checkStatus(): void
    {
        if (!$this->currentSubmission) {
            $this->isRunning = false;
            return;
        }

        $this->currentSubmission->refresh();
        
        // Update test results even while running to show progress
        $this->testResults = $this->currentSubmission->testResults->map(fn($r) => [
            'name' => $r->testCase->name,
            'passed' => $r->passed,
            'message' => $r->message,
            'earned_weight' => $r->earned_weight,
            'status' => $r->duration_ms > 0 ? 'completed' : 'pending' // duration_ms > 0 means it was actually run
        ])->toArray();

        if ($this->currentSubmission->status === PracticeSubmission::STATUS_COMPLETED) {
            $this->isRunning = false;
            $this->showResults = true;
        } elseif (in_array($this->currentSubmission->status, ['failed', 'timeout'])) {
            $this->isRunning = false;
            $this->showResults = true;
        }
    }

    #[Computed]
    public function practiceSummary(): array
    {
        return [
            'best_score' => $this->bestSubmission?->score,
            'passed' => $this->bestSubmission?->passed,
            'attempts' => $this->attemptCount,
            'is_running' => $this->isRunning,
        ];
    }

    public function render(): View
    {
        return view('livewire.practice-editor');
    }
}