<?php

namespace App\Livewire\Teacher\Modules;

use App\Models\Course;
use App\Models\Module;
use App\Models\Practice;
use App\Models\PracticeTestCase;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Edit extends Component
{
    use WithFileUploads;

    public Module $module;
    public $title;
    public $course_id;
    public $position;

    // Practice properties
    public ?Practice $practice = null;
    public bool $practiceEnabled = false;
    public string $practiceTitle = '';
    public string $practiceDescription = '';
    public float $practiceMaxScore = 10.0;
    public float $practicePassScore = 7.0;
    public bool $practiceIsActive = true;
    public string $practiceObjective = '';
    public string $practiceTechnicalTask = '';
    public string $practiceCheckingCriteria = '';
    public $practiceResultImage = null;
    public ?string $existingResultImagePath = null;
    public array $practiceTestCases = [];

    public string $activeTab = 'content';

    public function mount(Module $module)
    {
        $this->module = $module;
        $this->title = $module->title;
        $this->course_id = $module->course_id;
        $this->position = $module->position;

        $this->loadPractice();
    }

    private function loadPractice(): void
    {
        $this->practice = $this->module->practices()->first();

        if ($this->practice) {
            $this->practiceEnabled = (bool) $this->practice->is_active;
            $this->practiceTitle = $this->practice->title;
            $this->practiceDescription = $this->practice->description ?? '';
            $this->practiceMaxScore = (float) $this->practice->max_score;
            $this->practicePassScore = (float) $this->practice->pass_score;
            $this->practiceIsActive = (bool) $this->practice->is_active;
            $this->practiceObjective = $this->practice->objective ?? '';
            $this->practiceTechnicalTask = $this->practice->technical_task ?? '';
            $this->practiceCheckingCriteria = $this->practice->checking_criteria ?? '';
            $this->existingResultImagePath = $this->practice->result_image_path;

            $this->practiceTestCases = $this->practice->testCases->map(fn($tc) => [
                'id' => Str::uuid()->toString(),
                'existing_id' => $tc->id,
                'name' => $tc->name,
                'type' => $tc->type,
                'weight' => (float) $tc->weight,
                'script' => json_encode($tc->script, JSON_UNESCAPED_UNICODE),
                'timeout_ms' => $tc->timeout_ms,
                'is_required' => (bool) $tc->is_required,
                'sort_order' => $tc->sort_order,
            ])->toArray();
        }
    }

    public function addTestCase(): void
    {
        $this->practiceTestCases[] = [
            'id' => Str::uuid()->toString(),
            'existing_id' => null,
            'name' => '',
            'type' => 'dom',
            'weight' => 2.0,
            'script' => '{"selector": ".element", "exists": true}',
            'timeout_ms' => 1000,
            'is_required' => false,
            'sort_order' => count($this->practiceTestCases),
        ];
    }

    public function removeTestCase(string $id): void
    {
        $this->practiceTestCases = array_values(
            array_filter($this->practiceTestCases, fn($tc) => $tc['id'] !== $id)
        );
    }

    protected $rules = [
        'title' => 'required|min:3|max:255',
        'course_id' => 'required|exists:courses,id',
        'position' => 'required|integer|min:1',
    ];

    public function save()
    {
        $this->validate();

        $this->module->update([
            'title' => $this->title,
            'course_id' => $this->course_id,
            'position' => $this->position,
        ]);

        if ($this->practiceEnabled) {
            $practice = Practice::updateOrCreate(
                ['practicable_type' => \App\Models\Module::class, 'practicable_id' => $this->module->id],
                [
                    'title' => $this->practiceTitle,
                    'description' => $this->practiceDescription,
                    'runner_profile' => 'frontend_html_css_js_v1',
                    'max_score' => $this->practiceMaxScore,
                    'pass_score' => $this->practicePassScore,
                    'is_active' => true,
                    'objective' => $this->practiceObjective,
                    'technical_task' => $this->practiceTechnicalTask,
                    'checking_criteria' => $this->practiceCheckingCriteria,
                ]
            );

            if ($this->practiceResultImage) {
                $imagePath = $this->practiceResultImage->store('practices', 'public');
                $practice->update(['result_image_path' => $imagePath]);
            }

            $existingIds = collect($this->practiceTestCases)
                ->pluck('existing_id')
                ->filter()
                ->toArray();

            PracticeTestCase::where('practice_id', $practice->id)
                ->whereNotIn('id', $existingIds)
                ->delete();

            foreach ($this->practiceTestCases as $index => $tc) {
                if (empty($tc['name'])) {
                    continue;
                }

                $script = json_decode($tc['script'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $script = [];
                }

                PracticeTestCase::updateOrCreate(
                    ['id' => $tc['existing_id'] ?? null],
                    [
                        'practice_id' => $practice->id,
                        'name' => $tc['name'],
                        'type' => $tc['type'],
                        'weight' => (float) $tc['weight'],
                        'script' => $script,
                        'timeout_ms' => (int) $tc['timeout_ms'],
                        'is_required' => (bool) $tc['is_required'],
                        'sort_order' => $index,
                        'version' => '1.0',
                    ]
                );
            }
        } elseif ($this->practice) {
            $this->practice->update(['is_active' => false]);
        }

        session()->flash('success', 'Модуль успешно обновлен.');

        return redirect()->route('teacher.modules.index');
    }

    public function render()
    {
        return view('livewire.teacher.modules.edit', [
            'courses' => Course::orderBy('title')->get(),
        ]);
    }
}
