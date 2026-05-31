<?php

namespace App\Livewire\Teacher\Lessons;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

#[Layout('layouts.app')]
class Create extends Component
{
    #[Rule('required|exists:courses,id')]
    public ?int $course_id = null;

    #[Rule('required|exists:modules,id')]
    public ?int $module_id = null;

    #[Rule('required|string|max:255')]
    public string $title = '';

    #[Rule('required|string|max:255|unique:lessons,slug')]
    public string $slug = '';

    #[Rule('required|string')]
    public string $content = '';

    #[Rule('boolean')]
    public bool $is_published = true;

    public ?int $insert_after_id = null;

    public function mount(): void
    {
        $this->insert_after_id = 0;
    }

    public function updatedCourseId(): void
    {
        $this->module_id = null;
        $this->insert_after_id = 0;
    }

    public function updatedTitle(string $value): void
    {
        $this->slug = Str::slug($value);
    }

    public function save()
    {
        $this->validate([
            'course_id' => 'required|exists:courses,id',
            'module_id' => 'required|exists:modules,id',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:lessons,slug',
            'content' => 'required|string',
            'is_published' => 'boolean',
        ]);

        $position = $this->calculatePosition();

        Lesson::create([
            'course_id' => $this->course_id,
            'module_id' => $this->module_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'position' => $position,
            'is_published' => $this->is_published,
        ]);

        session()->flash('success', 'Урок успешно создан.');

        return redirect()->route('teacher.lessons.index');
    }

    protected function calculatePosition(): float
    {
        $lessons = Lesson::where('course_id', $this->course_id)
            ->orderBy('position')
            ->get();

        if ($lessons->isEmpty()) {
            return 1.0;
        }

        if ($this->insert_after_id === null || $this->insert_after_id === 0) {
            return $lessons->first()->position / 2;
        }

        $afterLesson = $lessons->firstWhere('id', $this->insert_after_id);

        if (!$afterLesson) {
            return $lessons->last()->position + 1;
        }

        $nextLesson = $lessons->firstWhere('position', '>', $afterLesson->position);

        if (!$nextLesson) {
            return $afterLesson->position + 1;
        }

        return ($afterLesson->position + $nextLesson->position) / 2;
    }

    public function render()
    {
        $existingLessons = $this->course_id
            ? Lesson::where('course_id', $this->course_id)->orderBy('position')->get()
            : collect();

        return view('livewire.teacher.lessons.create', [
            'courses' => Course::all(),
            'modules' => $this->course_id ? Module::where('course_id', $this->course_id)->get() : collect(),
            'existingLessons' => $existingLessons,
        ]);
    }
}

