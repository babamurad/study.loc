<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Lesson;
use App\Models\Practice;

$lessons = Lesson::with('practice')->get();

foreach ($lessons as $lesson) {
    echo "Lesson ID: {$lesson->id}, Title: {$lesson->title}\n";
    if ($lesson->practice) {
        echo "  Practice ID: {$lesson->practice->id}, Active: " . ($lesson->practice->is_active ? 'Yes' : 'No') . "\n";
    } else {
        echo "  No Practice\n";
    }
    
    // Check all practices just in case
    $allPractices = Practice::where('practicable_id', $lesson->id)->where('practicable_type', Lesson::class)->get();
    if ($allPractices->count() > 1) {
        echo "  WARNING: Found {$allPractices->count()} practices for this lesson!\n";
        foreach ($allPractices as $p) {
             echo "    - Practice ID: {$p->id}, Active: " . ($p->is_active ? 'Yes' : 'No') . "\n";
        }
    }
}
