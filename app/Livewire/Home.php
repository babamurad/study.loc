<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.front')]
class Home extends Component
{
    public function render()
    {
        return view('livewire.home');
    }
}
