<?php

namespace App\Livewire\Teacher\Modules;

use App\Models\Module;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public $course_id;

    public function mount()
    {
        $this->course_id = request()->query('course_id');
    }

    public function deleteModule(Module $module)
    {
        $module->delete();
        session()->flash('success', 'Модуль успешно удален.');
    }

    public function render()
    {
        $query = Module::with('course')->withCount('lessons');

        if ($this->course_id) {
            $query->where('course_id', $this->course_id);
        }

        return view('livewire.teacher.modules.index', [
            'modules' => $query->orderBy('created_at', 'desc')->paginate(15),
            'course' => $this->course_id ? \App\Models\Course::find($this->course_id) : null,
        ]);
    }
}
