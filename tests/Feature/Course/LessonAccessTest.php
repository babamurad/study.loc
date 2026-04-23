<?php

namespace Tests\Feature\Course;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class LessonAccessTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected Course $course;
    protected User $user;
    protected array $lessons = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->course = Course::create([
            'title' => 'Test Course',
            'slug' => 'test-course',
            'is_published' => true,
        ]);

        $this->lessons[1] = Lesson::create([
            'course_id' => $this->course->id,
            'title' => 'Lesson 1',
            'slug' => 'lesson-1',
            'position' => 1,
            'is_published' => true,
        ]);

        $this->lessons[2] = Lesson::create([
            'course_id' => $this->course->id,
            'title' => 'Lesson 2',
            'slug' => 'lesson-2',
            'position' => 2,
            'is_published' => true,
        ]);

        $this->lessons[3] = Lesson::create([
            'course_id' => $this->course->id,
            'title' => 'Lesson 3',
            'slug' => 'lesson-3',
            'position' => 3,
            'is_published' => true,
        ]);

        $this->user = User::factory()->create();
    }

    public function test_first_lesson_is_accessible(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('lessons.show', ['course' => $this->course->id, 'lesson' => $this->lessons[1]->id]));

        $response->assertStatus(200);
    }

    public function test_second_lesson_is_locked_without_first_completion(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('lessons.show', ['course' => $this->course->id, 'lesson' => $this->lessons[2]->id]));

        $response->assertStatus(403);
    }

    public function test_second_lesson_is_accessible_after_first_completion(): void
    {
        $this->lessons[1]->progress()->create([
            'user_id' => $this->user->id,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('lessons.show', ['course' => $this->course->id, 'lesson' => $this->lessons[2]->id]));

        $response->assertStatus(200);
    }

    public function test_cannot_access_locked_lesson_directly(): void
    {
        $this->lessons[1]->progress()->create([
            'user_id' => $this->user->id,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('lessons.show', ['course' => $this->course->id, 'lesson' => $this->lessons[3]->id]));

        $response->assertStatus(403);
    }

    public function test_complete_lesson_creates_progress(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('lessons.complete', $this->lessons[1]->id));

        $response->assertRedirect();

        $this->assertDatabaseHas('user_lesson_progress', [
            'user_id' => $this->user->id,
            'lesson_id' => $this->lessons[1]->id,
            'status' => 'completed',
        ]);
    }

    public function test_complete_redirects_to_next_lesson(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('lessons.complete', $this->lessons[1]->id));

        $response->assertRedirect(route('lessons.show', [
            'course' => $this->course->id,
            'lesson' => $this->lessons[2]->id,
        ]));
    }

    public function test_complete_last_lesson_redirects_to_course(): void
    {
        $this->lessons[1]->progress()->create([
            'user_id' => $this->user->id,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('lessons.complete', $this->lessons[2]->id));

        $response->assertRedirect(route('courses.show', $this->course->id));
    }
}