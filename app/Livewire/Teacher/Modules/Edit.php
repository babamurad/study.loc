<?php

namespace App\Livewire\Teacher\Modules;

use App\Models\Course;
use App\Models\Module;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Edit extends Component
{
    public Module $module;
    public $title;
    public $course_id;
    public $position;

    public function mount(Module $module)
    {
        $this->module = $module;
        $this->title = $module->title;
        $this->course_id = $module->course_id;
        $this->position = $module->position;
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
