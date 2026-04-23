<?php

namespace Database\Factories;

use App\Models\LessonQuiz;
use App\Models\User;
use App\Models\UserQuizAttempt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserQuizAttempt>
 */
class UserQuizAttemptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'lesson_quiz_id' => LessonQuiz::factory(),
            'score' => $this->faker->numberBetween(0, 100),
            'passed' => $this->faker->boolean,
        ];
    }
}
