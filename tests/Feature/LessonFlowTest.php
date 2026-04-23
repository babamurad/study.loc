<?php

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Models\UserLessonProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\assertDatabaseHas;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->course = Course::factory()
        ->has(Lesson::factory()->state(['position' => 1]), 'lessons')
        ->has(Lesson::factory()->state(['position' => 2]), 'lessons')
        ->create();

    $this->lesson1 = $this->course->lessons()->where('position', 1)->first();
    $this->lesson2 = $this->course->lessons()->where('position', 2)->first();
});

test('unauthenticated user is redirected to login', function () {
    get(route('lessons.show', ['course' => $this->course, 'lesson' => $this->lesson1]))
        ->assertRedirect(route('login'));
});

test('user can access first lesson of a course', function () {
    actingAs($this->user)
        ->get(route('lessons.show', ['course' => $this->course, 'lesson' => $this->lesson1]))
        ->assertOk()
        ->assertSeeLivewire('lesson-show');

    Livewire::actingAs($this->user)
        ->test('lesson-show', ['course' => $this->course, 'lesson' => $this->lesson1])
        ->assertSee($this->lesson1->title);
});

test('user cannot access second lesson before completing first', function () {
    actingAs($this->user)
        ->get(route('lessons.show', ['course' => $this->course, 'lesson' => $this->lesson2]))
        ->assertForbidden();
});

test('user can access second lesson after completing first', function () {
    actingAs($this->user);

    Livewire::test('lesson-show', ['course' => $this->course, 'lesson' => $this->lesson1])
        ->call('complete');

    actingAs($this->user)
        ->get(route('lessons.show', ['course' => $this->course, 'lesson' => $this->lesson2]))
        ->assertOk()
        ->assertSeeLivewire('lesson-show');
});

test('completing a lesson marks it as completed in database', function () {
    actingAs($this->user);

    Livewire::test('lesson-show', ['course' => $this->course, 'lesson' => $this->lesson1])
        ->call('complete');

    assertDatabaseHas('user_lesson_progress', [
        'user_id' => $this->user->id,
        'lesson_id' => $this->lesson1->id,
        'status' => 'completed',
    ]);
});

test('completing a lesson twice does not create duplicate entries', function () {
    actingAs($this->user);

    $component = Livewire::test('lesson-show', ['course' => $this->course, 'lesson' => $this->lesson1]);

    $component->call('complete');
    $component->call('complete');

    $progressCount = UserLessonProgress::where('user_id', $this->user->id)
        ->where('lesson_id', $this->lesson1->id)
        ->count();

    expect($progressCount)->toBe(1);
});
