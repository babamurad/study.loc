<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\Practice;
use Illuminate\Database\Eloquent\Factories\Factory;

class PracticeFactory extends Factory
{
    protected $model = Practice::class;

    public function definition(): array
    {
        return [
            'practicable_type' => \App\Models\Lesson::class,
            'practicable_id' => \App\Models\Lesson::factory(),
            'title' => $this->faker->sentence(),
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