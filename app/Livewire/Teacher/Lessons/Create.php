<?php

namespace App\Livewire\Teacher\Lessons;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Create extends Component
{
    public $course_id;
    public $module_id;
    public $title;
    public $slug;
    public $content;
    public $position = 0;
    public $is_published = true;

    protected $rules = [
        'course_id' => 'required|exists:courses,id',
        'module_id' => 'required|exists:modules,id',
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:lessons,slug',
        'content' => 'required|string',
        'position' => 'required|integer|min:0',
        'is_published' => 'boolean',
    ];

    public function updatedCourseId(): void
    {
        $this->module_id = null;
    }

    public function updatedTitle($value): void
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
