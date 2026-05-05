<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Practice;

$practices = Practice::all();

echo "Total Practices: " . $practices->count() . "\n";

foreach ($practices as $p) {
    echo "ID: {$p->id}, Title: {$p->title}, Type: {$p->practicable_type}, ID: {$p->practicable_id}, Active: " . ($p->is_active ? 'Yes' : 'No') . "\n";
}
