<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$apiKey = config('services.gemini.api_key');
if (!$apiKey) {
    echo "API Key is missing!\n";
    exit;
}

$url = "https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}";
$response = Http::get($url);

$data = json_decode($response->body(), true);
if (isset($data['models'])) {
    foreach ($data['models'] as $model) {
        if (str_contains($model['name'], 'gemini')) {
            echo $model['name'] . "\n";
        }
    }
} else {
    echo $response->body();
}
