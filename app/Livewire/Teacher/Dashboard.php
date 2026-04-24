<?php

namespace App\Livewire\Teacher;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.teacher.dashboard');
    }
}
