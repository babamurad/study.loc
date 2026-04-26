<?php

namespace App\Livewire\Teacher\Practices;

use App\Models\Lesson;
use App\Models\LessonPractice;
use App\Models\PracticeTestCase;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Illuminate\Support\Str;

class EditPractice extends Component
{
    public ?LessonPractice $practice = null;
    public Lesson $lesson;
    
    public string $title = '';
    public string $description = '';
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
    
    public function mount(Lesson $lesson, ?LessonPractice $practice = null): void
    {
        $this->lesson = $lesson;
        $this->practice = $practice;
        
        if ($practice) {
            $this->title = $practice->title;
            $this->description = $practice->description ?? '';
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
        
        $practice = LessonPractice::updateOrCreate(
            ['id' => $this->practice?->id],
            [
                'lesson_id' => $this->lesson->id,
                'title' => $this->title,
                'description' => $this->description,
                'runner_profile' => $this->runnerProfile,
                'max_score' => $this->maxScore,
                'pass_score' => $this->passScore,
                'is_active' => $this->isActive,
            ]
        );
        
        $existingIds = collect($this->testCases)
            ->pluck('existing_id')
            ->filter()
            ->toArray();
        
        PracticeTestCase::where('lesson_practice_id', $practice->id)
            ->whereNotIn('id', $existingIds)
            ->delete();
        
        foreach ($this->testCases as $index => $tc) {
            if (empty($tc['name'])) {
                continue;
            }
            
            PracticeTestCase::updateOrCreate(
                ['id' => $tc['existing_id'] ?? null],
                [
                    'lesson_practice_id' => $practice->id,
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