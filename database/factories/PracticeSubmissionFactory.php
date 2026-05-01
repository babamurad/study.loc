<?php

namespace Database\Factories;

use App\Models\LessonPractice;
use App\Models\PracticeSubmission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PracticeSubmissionFactory extends Factory
{
    protected $model = PracticeSubmission::class;

    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'practice_id' => \App\Models\Practice::factory(),
            'html_code' => '<div></div>',
            'css_code' => '.card { width: 300px; }',
            'js_code' => null,
            'status' => PracticeSubmission::STATUS_PENDING,
            'score' => null,
            'passed' => false,
            'attempt_no' => 1,
            'runner_job_id' => null,
            'runner_version' => null,
            'started_at' => null,
            'checked_at' => null,
            'error_message' => null,
            'raw_result' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PracticeSubmission::STATUS_COMPLETED,
            'score' => $this->faker->randomFloat(1, 5, 10),
            'passed' => true,
            'started_at' => now(),
            'checked_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PracticeSubmission::STATUS_FAILED,
            'score' => 0.0,
            'passed' => false,
            'started_at' => now(),
            'checked_at' => now(),
        ]);
    }
}