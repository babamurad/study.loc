<?php

namespace App\Livewire\Teacher\Courses;

use App\Models\Course;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Create extends Component
{
    public $title = '';
    public $slug = '';
    public $description = '';
    public $is_published = true;

    protected $rules = [
        'title' => 'required|min:3|max:255',
        'slug' => 'required|unique:courses,slug',
        'description' => 'nullable|min:10',
        'is_published' => 'boolean',
    ];

    public function updatedTitle($value)
    {
        $this->slug = Str::slug($value);
    }

    public function save()
    {
        $this->validate();

        Course::create([
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_published' => $this->is_published,
        ]);

        session()->flash('success', 'Курс успешно создан.');

        return redirect()->route('teacher.courses.index');
    }

    public function render()
    {
        return view('livewire.teacher.courses.create');
    }
}
