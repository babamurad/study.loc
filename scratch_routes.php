<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$app->instance('request', $request);

$routeCollection = Route::getRoutes();

foreach ($routeCollection as $value) {
    echo $value->getName() . ' | ' . $value->uri() . ' | ' . implode(',', $value->methods()) . PHP_EOL;
}
