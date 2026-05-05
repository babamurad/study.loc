<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "Table 'practices' exists: " . (Schema::hasTable('practices') ? 'Yes' : 'No') . "\n";
if (Schema::hasTable('practices')) {
    echo "Columns: " . implode(', ', Schema::getColumnListing('practices')) . "\n";
}

echo "Table 'lesson_practices' exists: " . (Schema::hasTable('lesson_practices') ? 'Yes' : 'No') . "\n";
