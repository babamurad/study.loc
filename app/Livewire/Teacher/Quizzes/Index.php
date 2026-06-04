<?php

namespace App\Livewire\Teacher\Quizzes;

use App\Models\Quiz;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public int $perPage = 10;

    #[Url]
    public string $search = '';

    protected function queryString(): array
    {
        return [
            'perPage' => ['except' => 10, 'as' => 'per_page'],
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function deleteQuiz(Quiz $quiz): void
    {
        $quiz->delete();
        session()->flash('success', 'Тест успешно удален.');
    }

    public function render()
    {
        $query = Quiz::withCount('questions')->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.teacher.quizzes.index', [
            'quizzes' => $query->paginate($this->perPage),
        ]);
    }
}
