<?php

use App\Livewire\LessonShow;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use App\Models\User;
use App\Models\UserLessonProgress;
use App\Models\UserQuizAttempt;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->course = Course::factory()->create();
    $this->lesson1 = Lesson::factory()->create(['course_id' => $this->course->id, 'position' => 1]);
    $this->lesson2 = Lesson::factory()->create(['course_id' => $this->course->id, 'position' => 2]);
    $this->quiz = Quiz::factory()->create();
    $this->lesson1->update(['quiz_id' => $this->quiz->id]);
    $question = QuizQuestion::factory()->create(['quiz_id' => $this->quiz->id]);
    QuizAnswer::factory()->create(['quiz_question_id' => $question->id, 'is_correct' => true]);
    QuizAnswer::factory()->create(['quiz_question_id' => $question->id, 'is_correct' => false]);

    actingAs($this->user);
});

test('user cannot access next lesson if quiz is not passed', function () {
    UserLessonProgress::factory()->create([
        'user_id' => $this->user->id,
        'lesson_id' => $this->lesson1->id,
        'status' => 'completed',
    ]);

    get(route('lessons.show', ['course' => $this->course, 'lesson' => $this->lesson2]))
        ->assertForbidden();
});

test('user can access next lesson after passing quiz', function () {
    UserLessonProgress::factory()->create([
        'user_id' => $this->user->id,
        'lesson_id' => $this->lesson1->id,
        'status' => 'completed',
    ]);

    UserQuizAttempt::factory()->create([
        'user_id' => $this->user->id,
        'quiz_id' => $this->quiz->id,
        'passed' => true,
    ]);

    get(route('lessons.show', ['course' => $this->course, 'lesson' => $this->lesson2]))
        ->assertOk();
});

test('user can complete lesson by passing quiz', function () {
    Livewire::test(\App\Livewire\Quizzes\Show::class, ['quiz' => $this->quiz])
        ->call('startQuiz')
        ->set('userAnswers.'.$this->quiz->questions->first()->id, $this->quiz->questions->first()->answers->where('is_correct', true)->first()->id)
        ->call('submitQuiz')
        ->assertHasNoErrors();
        
    assertDatabaseHas('user_quiz_attempts', [
        'user_id' => $this->user->id,
        'quiz_id' => $this->quiz->id,
        'passed' => true,
    ]);

    Livewire::test(LessonShow::class, ['course' => $this->course, 'lesson' => $this->lesson1])
        ->call('complete')
        ->assertHasNoErrors();

    assertDatabaseHas('user_lesson_progress', [
        'user_id' => $this->user->id,
        'lesson_id' => $this->lesson1->id,
        'status' => 'completed',
    ]);
});

test('user cannot complete lesson by failing quiz', function () {
    Livewire::test(\App\Livewire\Quizzes\Show::class, ['quiz' => $this->quiz])
        ->call('startQuiz')
        ->set('userAnswers.'.$this->quiz->questions->first()->id, $this->quiz->questions->first()->answers->where('is_correct', false)->first()->id)
        ->call('submitQuiz')
        ->assertHasNoErrors();

    assertDatabaseHas('user_quiz_attempts', [
        'user_id' => $this->user->id,
        'quiz_id' => $this->quiz->id,
        'passed' => false,
    ]);

    Livewire::test(LessonShow::class, ['course' => $this->course, 'lesson' => $this->lesson1])
        ->call('complete')
        ->assertHasNoErrors();

    assertDatabaseMissing('user_lesson_progress', [
        'user_id' => $this->user->id,
        'lesson_id' => $this->lesson1->id,
        'status' => 'completed',
    ]);
});
