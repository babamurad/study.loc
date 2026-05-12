<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$apiKey = config('services.gemini.api_key');
$model = 'gemini-2.5-flash';

$schema = [
    'type' => 'OBJECT',
    'properties' => [
        'results' => [
            'type' => 'ARRAY',
            'items' => [
                'type' => 'OBJECT',
                'properties' => [
                    'id' => ['type' => 'INTEGER'],
                    'passed' => ['type' => 'BOOLEAN'],
                    'message' => ['type' => 'STRING'],
                ],
                'required' => ['id', 'passed', 'message'],
            ],
        ],
    ],
    'required' => ['results'],
];

$payloadCamel = [
    'systemInstruction' => [
        'parts' => [['text' => 'You are a bot. Return JSON.']],
    ],
    'contents' => [
        [
            'role' => 'user',
            'parts' => [['text' => 'Test']],
        ],
    ],
    'generationConfig' => [
        'responseMimeType' => 'application/json',
        'responseSchema' => $schema,
        'temperature' => 0.2,
    ]
];

$url = "https://generativelanguage.googleapis.com/v1/models/{$model}:generateContent?key={$apiKey}";

echo "Testing v1 camelCase payload...\n";
$responseCamel = Http::post($url, $payloadCamel);
echo "Status: " . $responseCamel->status() . "\n";
echo "Response: " . substr($responseCamel->body(), 0, 300) . "...\n";
