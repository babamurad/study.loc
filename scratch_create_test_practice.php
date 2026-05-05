<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Lesson;
use App\Models\Practice;

$lesson = Lesson::first();
if ($lesson) {
    $practice = Practice::create([
        'practicable_type' => Lesson::class,
        'practicable_id' => $lesson->id,
        'title' => 'Test Practice',
        'is_active' => true,
        'runner_profile' => 'frontend_html_css_js_v1',
        'max_score' => 10,
        'pass_score' => 7,
    ]);
    echo "Created practice ID: {$practice->id} for Lesson ID: {$lesson->id}\n";
} else {
    echo "No lessons found\n";
}
