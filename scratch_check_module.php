<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Lesson;

$l = Lesson::with('module')->find(1);
if ($l) {
    echo "Lesson 1: " . $l->title . "\n";
    echo "Module: " . ($l->module ? $l->module->title : 'NULL') . "\n";
} else {
    echo "Lesson 1 not found\n";
}
