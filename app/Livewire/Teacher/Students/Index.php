<?php

declare(strict_types=1);

namespace App\Livewire\Teacher\Students;

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\User;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

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

    public function render(): View
    {
        $studentsQuery = User::query()
            ->where('role', UserRole::Student)
            ->with(['completedLessons']);

        if ($this->search) {
            $studentsQuery->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $students = $studentsQuery->paginate($this->perPage);
        $courses = Course::withCount('lessons')->where('is_published', true)->get();

        return view('livewire.teacher.students.index', [
            'students' => $students,
            'courses' => $courses,
        ]);
    }
}
