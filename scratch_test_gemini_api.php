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

// Payload with snake_case (current implementation in GeminiEvaluator.php)
$payloadSnake = [
    'systemInstruction' => [ // Wait, the original code uses systemInstruction
        'parts' => [['text' => 'You are a bot. Return JSON.']],
    ],
    'contents' => [
        [
            'role' => 'user',
            'parts' => [['text' => 'Test']],
        ],
    ],
    'generationConfig' => [
        'response_mime_type' => 'application/json',
        'response_schema' => $schema,
        'temperature' => 0.2,
    ]
];

// Payload with camelCase
$payloadCamel = [
    'system_instruction' => [
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

echo "Testing snake_case payload...\n";
$responseSnake = Http::post($url, $payloadSnake);
echo "Status: " . $responseSnake->status() . "\n";
echo "Response: " . substr($responseSnake->body(), 0, 300) . "...\n\n";

echo "Testing camelCase payload...\n";
$responseCamel = Http::post($url, $payloadCamel);
echo "Status: " . $responseCamel->status() . "\n";
echo "Response: " . substr($responseCamel->body(), 0, 300) . "...\n";
