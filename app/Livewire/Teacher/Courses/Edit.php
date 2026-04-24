<?php

namespace App\Livewire\Teacher\Courses;

use App\Models\Course;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Edit extends Component
{
    public Course $course;
    public $title;
    public $slug;
    public $description;
    public $is_published;

    public function mount(Course $course)
    {
        $this->course = $course;
        $this->title = $course->title;
        $this->slug = $course->slug;
        $this->description = $course->description;
        $this->is_published = $course->is_published;
    }

    protected function rules()
    {
        return [
            'title' => 'required|min:3|max:255',
            'slug' => 'required|unique:courses,slug,' . $this->course->id,
            'description' => 'nullable|min:10',
            'is_published' => 'boolean',
        ];
    }

    public function save()
    {
        $this->validate();

        $this->course->update([
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_published' => $this->is_published,
        ]);

        session()->flash('success', 'Курс успешно обновлен.');

        return redirect()->route('teacher.courses.index');
    }

    public function render()
    {
        return view('livewire.teacher.courses.edit');
    }
}
