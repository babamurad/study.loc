<?php

namespace Database\Factories;

use App\Models\LessonPractice;
use App\Models\PracticeTestCase;
use Illuminate\Database\Eloquent\Factories\Factory;

class PracticeTestCaseFactory extends Factory
{
    protected $model = PracticeTestCase::class;

    public function definition(): array
    {
        return [
            'practice_id' => \App\Models\Practice::factory(),
            'name' => $this->faker->words(3, true),
            'type' => $this->faker->randomElement(PracticeTestCase::TYPES),
            'weight' => $this->faker->randomFloat(1, 1, 3),
            'script' => [
                'selector' => '.card',
                'exists' => true,
            ],
            'timeout_ms' => 1000,
            'sort_order' => 0,
            'is_required' => false,
            'version' => '1.0',
        ];
    }

    public function required(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_required' => true,
        ]);
    }
}