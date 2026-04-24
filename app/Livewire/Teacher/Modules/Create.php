<?php

namespace App\Livewire\Teacher\Modules;

use App\Models\Course;
use App\Models\Module;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Create extends Component
{
    public $title = '';
    public $course_id = '';
    public $position = 1;

    protected $rules = [
        'title' => 'required|min:3|max:255',
        'course_id' => 'required|exists:courses,id',
        'position' => 'required|integer|min:1',
    ];

    public function mount()
    {
        // Try to get course_id from query string if available
        $this->course_id = request()->query('course_id', '');
        
        if ($this->course_id) {
            $lastModule = Module::where('course_id', $this->course_id)->orderBy('position', 'desc')->first();
            $this->position = $lastModule ? $lastModule->position + 1 : 1;
        }
    }

    public function updatedCourseId($value)
    {
        if ($value) {
            $lastModule = Module::where('course_id', $value)->orderBy('position', 'desc')->first();
            $this->position = $lastModule ? $lastModule->position + 1 : 1;
        }
    }

    public function save()
    {
        $this->validate();

        Module::create([
            'title' => $this->title,
            'course_id' => $this->course_id,
            'position' => $this->position,
        ]);

        session()->flash('success', 'Модуль успешно создан.');

        return redirect()->route('teacher.modules.index');
    }

    public function render()
    {
        return view('livewire.teacher.modules.create', [
            'courses' => Course::orderBy('title')->get(),
        ]);
    }
}
