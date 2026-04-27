<?php

namespace App\Livewire\Teacher\Lessons;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonPractice;
use App\Models\Module;
use App\Models\PracticeTestCase;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\Rule;

#[Layout('layouts.app')]
class Edit extends Component
{
    public Lesson $lesson;

    #[Rule('required|exists:courses,id')]
    public ?int $course_id = null;

    #[Rule('required|exists:modules,id')]
    public ?int $module_id = null;

    #[Rule('required|string|max:255')]
    public string $title = '';

    #[Rule('required|string|max:255')]
    public string $slug = '';

    #[Rule('required|string')]
    public string $content = '';

    #[Rule('required|integer|min:0')]
    public int $position = 0;

    #[Rule('boolean')]
    public bool $is_published = false;

    // Practice properties
    public ?LessonPractice $practice = null;

    #[Rule('boolean')]
    public bool $practiceEnabled = false;

    #[Rule('required_if:practiceEnabled,true|string|max:255')]
    public string $practiceTitle = '';

    public string $practiceDescription = '';

    #[Rule('required_if:practiceEnabled,true|numeric|min:1')]
    public float $practiceMaxScore = 10.0;

    #[Rule('required_if:practiceEnabled,true|numeric|min:0|lte:practiceMaxScore')]
    public float $practicePassScore = 7.0;

    public bool $practiceIsActive = true;
    public array $practiceTestCases = [];

    #[Url]
    public int $page = 1;

    public string $activeTab = 'content';

    public function mount(Lesson $lesson): void
    {
        $this->lesson = $lesson;
        $this->course_id = $lesson->course_id;
        $this->module_id = $lesson->module_id;
        $this->title = $lesson->title;
        $this->slug = $lesson->slug;
        $this->content = $lesson->content;
        $this->position = (int) $lesson->position;
        $this->is_published = (bool) $lesson->is_published;

        $this->loadPractice();
    }

    private function loadPractice(): void
    {
        $this->practice = $this->lesson->practice;

        if ($this->practice) {
            $this->practiceEnabled = (bool) $this->practice->is_active;
            $this->practiceTitle = $this->practice->title;
            $this->practiceDescription = $this->practice->description ?? '';
            $this->practiceMaxScore = (float) $this->practice->max_score;
            $this->practicePassScore = (float) $this->practice->pass_score;
            $this->practiceIsActive = (bool) $this->practice->is_active;

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

    public function updatedTitle(string $value): void
    {
        $this->slug = Str::slug($value);
    }

    public function save()
    {
        $this->validate();

        $this->lesson->update([
            'course_id' => $this->course_id,
            'module_id' => $this->module_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'position' => $this->position,
            'is_published' => $this->is_published,
        ]);

        if ($this->practiceEnabled) {
            $practice = LessonPractice::updateOrCreate(
                ['lesson_id' => $this->lesson->id],
                [
                    'title' => $this->practiceTitle,
                    'description' => $this->practiceDescription,
                    'runner_profile' => 'frontend_html_css_js_v1',
                    'max_score' => $this->practiceMaxScore,
                    'pass_score' => $this->practicePassScore,
                    'is_active' => true,
                ]
            );

            $existingIds = collect($this->practiceTestCases)
                ->pluck('existing_id')
                ->filter()
                ->toArray();

            PracticeTestCase::where('lesson_practice_id', $practice->id)
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
                        'lesson_practice_id' => $practice->id,
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

        session()->flash('success', 'Урок успешно обновлен.');

        return redirect()->route('teacher.lessons.index', ['page' => $this->page]);
    }

    public function render()
    {
        return view('livewire.teacher.lessons.edit', [
            'courses' => Course::all(),
            'modules' => $this->course_id ? Module::where('course_id', $this->course_id)->get() : collect(),
        ]);
    }
}

