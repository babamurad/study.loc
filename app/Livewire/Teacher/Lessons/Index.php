<?php

namespace App\Livewire\Teacher\Lessons;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\Session;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    #[Session]
    public int $perPage = 10;

    #[Url]
    #[Session]
    public ?int $course_id = null;

    #[Url]
    #[Session]
    public ?int $module_id = null;

    #[Url]
    #[Session]
    public string $search = '';

    #[Url]
    #[Session]
    public string $sortField = 'created_at';

    #[Url]
    #[Session]
    public string $sortDirection = 'desc';

    #[Session]
    public array $selectedColumns = [
        'course_id' => true,
        'module_id' => true,
        'position' => true,
        'is_published' => true,
    ];

    protected function queryString(): array
    {
        return [
            'perPage' => ['except' => 10, 'as' => 'per_page'],
        ];
    }

    public function updatedCourseId(): void
    {
        $this->module_id = null;
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedModuleId(): void
    {
        $this->resetPage();
    }

    public function deleteLesson(Lesson $lesson): void
    {
        $lesson->delete();
        session()->flash('success', 'Урок успешно удален.');
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $query = Lesson::with(['course', 'module'])->orderBy($this->sortField, $this->sortDirection);

        if ($this->course_id) {
            $query->where('course_id', $this->course_id);
        }

        if ($this->module_id) {
            $query->where('module_id', $this->module_id);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('content', 'like', '%' . $this->search . '%');
            });
        }

        $courses = Course::orderBy('title')->get();
        $modules = $this->course_id ? Module::where('course_id', $this->course_id)->orderBy('title')->get() : collect();

        return view('livewire.teacher.lessons.index', [
            'lessons' => $query->paginate($this->perPage),
            'courses' => $courses,
            'modules' => $modules,
        ]);
    }
}
