<?php

namespace App\Livewire\Teacher\Lessons;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
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
    public ?int $course_id = null;

    #[Url]
    public ?int $module_id = null;

    #[Url]
    public string $search = '';

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

    public function render()
    {
        $query = Lesson::with(['course', 'module'])->orderBy('created_at', 'desc');

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
