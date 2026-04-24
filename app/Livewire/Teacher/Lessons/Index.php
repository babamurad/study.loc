<?php

namespace App\Livewire\Teacher\Lessons;

use App\Models\Lesson;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public function deleteLesson(Lesson $lesson)
    {
        $lesson->delete();
        session()->flash('success', 'Урок успешно удален.');
    }

    public function render()
    {
        return view('livewire.teacher.lessons.index', [
            'lessons' => Lesson::with(['course', 'module'])->orderBy('created_at', 'desc')->paginate(10),
        ]);
    }
}
