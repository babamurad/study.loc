<?php

namespace App\Livewire\Teacher\Practices;

use App\Models\Lesson;
use App\Models\Practice;
use App\Models\PracticeTestCase;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

class EditPractice extends Component
{
    use WithFileUploads;

    public ?Practice $practice = null;
    public Lesson $lesson;
    
    public string $title = '';
    public string $description = '';
    public string $objective = '';
    public string $technicalTask = '';
    public string $checkingCriteria = '';
    public $resultImage = null;
    public ?string $existingResultImagePath = null;
    public string $runnerProfile = 'frontend_html_css_js_v1';
    public float $maxScore = 10.0;
    public float $passScore = 7.0;
    public bool $isActive = true;
    
    public array $testCases = [];
    
    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'runnerProfile' => 'required|string',
        'maxScore' => 'required|numeric|min:1|max:100',
        'passScore' => 'required|numeric|min:0|max:maxScore',
        'isActive' => 'boolean',
        'testCases' => 'array',
    ];
    
    public function mount(Lesson $lesson, ?Practice $practice = null): void
    {
        $this->lesson = $lesson;
        $this->practice = $practice;
        
        if ($practice) {
            $this->title = $practice->title;
            $this->description = $practice->description ?? '';
            $this->objective = $practice->objective ?? '';
            $this->technicalTask = $practice->technical_task ?? '';
            $this->checkingCriteria = $practice->checking_criteria ?? '';
            $this->existingResultImagePath = $practice->result_image_path;
            $this->runnerProfile = $practice->runner_profile;
            $this->maxScore = (float) $practice->max_score;
            $this->passScore = (float) $practice->pass_score;
            $this->isActive = $practice->is_active;
            
            $this->testCases = $practice->testCases->map(fn($tc) => [
                'id' => Str::uuid()->toString(),
                'existing_id' => $tc->id,
                'name' => $tc->name,
                'type' => $tc->type,
                'weight' => (float) $tc->weight,
                'script' => json_encode($tc->script),
                'timeout_ms' => $tc->timeout_ms,
                'is_required' => $tc->is_required,
                'sort_order' => $tc->sort_order,
            ])->toArray();
        }
    }
    
    public function addTestCase(): void
    {
        $this->testCases[] = [
            'id' => Str::uuid()->toString(),
            'existing_id' => null,
            'name' => '',
            'type' => 'dom',
            'weight' => 2.0,
            'script' => '{"selector": ".card", "exists": true}',
            'timeout_ms' => 1000,
            'is_required' => false,
            'sort_order' => count($this->testCases),
        ];
    }
    
    public function removeTestCase(string $id): void
    {
        $this->testCases = array_values(array_filter($this->testCases, fn($tc) => $tc['id'] !== $id));
    }
    
    public function save(): void
    {
        $this->validate();
        
        $practice = Practice::updateOrCreate(
            ['id' => $this->practice?->id],
            [
                'practicable_type' => \App\Models\Lesson::class,
                'practicable_id' => $this->lesson->id,
                'title' => $this->title,
                'description' => $this->description,
                'objective' => $this->objective,
                'technical_task' => $this->technicalTask,
                'checking_criteria' => $this->checkingCriteria,
                'runner_profile' => $this->runnerProfile,
                'max_score' => $this->maxScore,
                'pass_score' => $this->passScore,
                'is_active' => $this->isActive,
            ]
        );
        
        if ($this->resultImage) {
            $imagePath = $this->resultImage->store('practices', 'public');
            $practice->update(['result_image_path' => $imagePath]);
            $this->existingResultImagePath = $imagePath;
            $this->resultImage = null;
        }
        
        $existingIds = collect($this->testCases)
            ->pluck('existing_id')
            ->filter()
            ->toArray();
        
        PracticeTestCase::where('practice_id', $practice->id)
            ->whereNotIn('id', $existingIds)
            ->delete();
        
        foreach ($this->testCases as $index => $tc) {
            if (empty($tc['name'])) {
                continue;
            }
            
            PracticeTestCase::updateOrCreate(
                ['id' => $tc['existing_id'] ?? null],
                [
                    'practice_id' => $practice->id,
                    'name' => $tc['name'],
                    'type' => $tc['type'],
                    'weight' => $tc['weight'],
                    'script' => json_decode($tc['script'], true) ?? [],
                    'timeout_ms' => $tc['timeout_ms'],
                    'is_required' => $tc['is_required'],
                    'sort_order' => $index,
                ]
            );
        }
        
        $this->practice = $practice;
        session()->flash('success', 'Практика сохранена!');
    }
    
    public function render(): View
    {
        return view('livewire.teacher.practices.edit-practice');
    }
}