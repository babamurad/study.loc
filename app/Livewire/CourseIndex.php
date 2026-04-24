<?php

namespace App\Livewire;

use App\Models\Course;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CourseIndex extends Component
{
    public function render()
    {
        return view('livewire.course-index', [
            'courses' => Course::all(),
        ]);
    }
}
