<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PracticeSubmission;

$submissions = PracticeSubmission::latest()->limit(5)->get();
foreach ($submissions as $s) {
    echo "ID: {$s->id}, Status: {$s->status}, Error: {$s->error_message}\n";
}
