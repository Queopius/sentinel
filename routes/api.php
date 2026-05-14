<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Queopius\Sentinel\Http\Controllers\CspReportController;
use Queopius\Sentinel\Http\Controllers\HealthController;

if ((bool) config('sentinel.csp_reports.enabled', false)) {
    Route::post((string) config('sentinel.csp_reports.route_path', 'sentinel/csp-reports'), [CspReportController::class, 'store'])
        ->middleware((array) config('sentinel.csp_reports.middleware', ['api']))
        ->name('sentinel.csp-reports.store');
}

if ((bool) config('sentinel.health_endpoint.enabled', false)) {
    Route::get((string) config('sentinel.health_endpoint.path', 'up/sentinel'), HealthController::class)
        ->middleware((array) config('sentinel.health_endpoint.middleware', ['api']))
        ->name('sentinel.health');
}
