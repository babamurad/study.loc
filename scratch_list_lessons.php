<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Lesson;

$lessons = Lesson::orderBy('id')->get(['id', 'title']);
foreach($lessons as $l) {
    echo "ID: {$l->id}, Title: {$l->title}\n";
}
