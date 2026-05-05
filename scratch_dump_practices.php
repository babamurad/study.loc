<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$rows = DB::table('practices')->get();
echo "Count: " . $rows->count() . "\n";
foreach ($rows as $row) {
    print_r($row);
}
