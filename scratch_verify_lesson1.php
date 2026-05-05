<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Lesson;
use App\Models\User;

$lesson = Lesson::find(1);
$user = User::first(); // Assuming there's a user

if ($lesson) {
    echo "Lesson: {$lesson->title}\n";
    $lesson->load('practice');
    if ($lesson->practice) {
        echo "Practice found: {$lesson->practice->title}\n";
        echo "Is Active: " . ($lesson->practice->is_active ? 'Yes' : 'No') . "\n";
    } else {
        echo "No practice found for this lesson record\n";
    }
} else {
    echo "Lesson 1 not found\n";
}
