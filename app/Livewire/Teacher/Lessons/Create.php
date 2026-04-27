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

    #[Rule('required|integer|min:0')]
    public int $position = 0;

    #[Rule('boolean')]
    public bool $is_published = true;

    public function updatedCourseId(): void
    {
        $this->module_id = null;
    }

    public function updatedTitle(string $value): void
    {
        $this->slug = Str::slug($value);
    }

    public function save()
    {
        $this->validate();

        Lesson::create([
            'course_id' => $this->course_id,
            'module_id' => $this->module_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'position' => $this->position,
            'is_published' => $this->is_published,
        ]);

        session()->flash('success', 'Урок успешно создан.');

        return redirect()->route('teacher.lessons.index');
    }

    public function render()
    {
        return view('livewire.teacher.lessons.create', [
            'courses' => Course::all(),
            'modules' => $this->course_id ? Module::where('course_id', $this->course_id)->get() : collect(),
        ]);
    }
}

