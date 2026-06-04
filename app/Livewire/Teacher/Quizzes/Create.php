<?php

namespace App\Livewire\Teacher\Quizzes;

use App\Models\Quiz;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

#[Layout('layouts.app')]
class Create extends Component
{
    #[Rule('required|string|max:255')]
    public string $title = '';

    public string $description = '';

    #[Rule('required|integer|min:0|max:100')]
    public int $pass_threshold = 70;

    public function save()
    {
        $this->validate();

        $quiz = Quiz::create([
            'title' => $this->title,
            'description' => $this->description,
            'pass_threshold' => $this->pass_threshold,
        ]);

        session()->flash('success', 'Тест успешно создан. Теперь вы можете добавить вопросы.');

        return redirect()->route('teacher.quizzes.edit', $quiz);
    }

    public function render()
    {
        return view('livewire.teacher.quizzes.create');
    }
}
