<?php

use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::get('health', HealthCheckResultsController::class);
