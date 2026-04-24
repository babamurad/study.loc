<?php

namespace App\Livewire\Teacher\Lessons;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[Layout('layouts.app')]
class Edit extends Component
{
    public Lesson $lesson;
    public $course_id;
    public $module_id;
    public $title;
    public $slug;
    public $content;
    public $position;
    public $is_published;

    #[Url]
    public $page = 1;

    protected $rules = [
        'course_id' => 'required|exists:courses,id',
        'module_id' => 'required|exists:modules,id',
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255',
        'content' => 'required|string',
        'position' => 'required|integer|min:0',
        'is_published' => 'boolean',
    ];

    public function mount(Lesson $lesson)
    {
        $this->lesson = $lesson;
        $this->course_id = $lesson->course_id;
        $this->module_id = $lesson->module_id;
        $this->title = $lesson->title;
        $this->slug = $lesson->slug;
        $this->content = $lesson->content;
        $this->position = $lesson->position;
        $this->is_published = $lesson->is_published;
    }

    public function updatedTitle($value)
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
