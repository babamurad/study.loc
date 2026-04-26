<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\LessonPractice;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonPracticeFactory extends Factory
{
    protected $model = LessonPractice::class;

    public function definition(): array
    {
        return [
            'lesson_id' => Lesson::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'runner_profile' => 'frontend_html_css_js_v1',
            'max_score' => 10.0,
            'pass_score' => 7.0,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}