<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Lesson;

$l = Lesson::find(1);
if ($l && $l->module) {
    echo "Module ID: " . $l->module_id . "\n";
    echo "Lessons in module: " . $l->module->lessons()->count() . "\n";
} else {
    echo "Lesson 1 or module not found\n";
}
