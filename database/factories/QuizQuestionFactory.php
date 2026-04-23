<?php

namespace Database\Factories;

use App\Models\LessonQuiz;
use App\Models\QuizQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuizQuestion>
 */
class QuizQuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lesson_quiz_id' => LessonQuiz::factory(),
            'question' => $this->faker->sentence . '?',
            'order' => 0,
        ];
    }
}
