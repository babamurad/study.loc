<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\Evaluators\GeminiEvaluator;
use App\Models\PracticeSubmission;
use App\Models\Practice;
use App\Models\PracticeTestCase;
use Mockery as m;

$tc1 = m::mock(PracticeTestCase::class)->makePartial();
$tc1->id = 1;
$tc1->name = 'Check H1 exists';
$tc1->type = 'html';
$tc1->script = 'h1';

$practice = m::mock(Practice::class)->makePartial();
$practice->shouldReceive('getAttribute')->with('testCases')->andReturn(collect([$tc1]));

$submission = m::mock(PracticeSubmission::class)->makePartial();
$submission->shouldReceive('getAttribute')->with('practice')->andReturn($practice);
$submission->shouldReceive('getAttribute')->with('html_code')->andReturn('<h1>Hello</h1>');
$submission->shouldReceive('getAttribute')->with('css_code')->andReturn('');
$submission->shouldReceive('getAttribute')->with('js_code')->andReturn('');

$evaluator = app(GeminiEvaluator::class);
try {
    $result = $evaluator->evaluate($submission);
    print_r($result);
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
