<?php

namespace App\Livewire\Teacher\Courses;

use App\Models\Course;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public function deleteCourse(Course $course)
    {
        $course->delete();
        session()->flash('success', 'Курс успешно удален.');
    }

    public function render()
    {
        return view('livewire.teacher.courses.index', [
            'courses' => Course::withCount('modules')->orderBy('created_at', 'desc')->paginate(10),
        ]);
    }
}
