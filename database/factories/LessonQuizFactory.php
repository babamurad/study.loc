<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\LessonQuiz;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LessonQuiz>
 */
class LessonQuizFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lesson_id' => Lesson::factory(),
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'pass_threshold' => 70,
        ];
    }
}
