<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Lesson;
use App\Models\Practice;

$lessons = Lesson::whereHas('practice')->get();
echo "Lessons with practices: " . $lessons->count() . "\n";
foreach($lessons as $l) {
    echo "ID: {$l->id}, Title: {$l->title}, Practice ID: {$l->practice->id}\n";
}
