<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Queopius\Shield\Http\Controllers\CspReportController;
use Queopius\Shield\Http\Controllers\HealthController;

if ((bool) config('shield.csp_reports.enabled', false)) {
    Route::post((string) config('shield.csp_reports.route_path', 'shield/csp-reports'), [CspReportController::class, 'store'])
        ->middleware((array) config('shield.csp_reports.middleware', ['api']))
        ->name('shield.csp-reports.store');
}

if ((bool) config('shield.health_endpoint.enabled', false)) {
    Route::get((string) config('shield.health_endpoint.path', 'up/shield'), HealthController::class)
        ->middleware((array) config('shield.health_endpoint.middleware', ['api']))
        ->name('shield.health');
}
