<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Queopius\Sentinel\Http\Controllers\DashboardController;

Route::group([
    'prefix' => (string) config('sentinel.ui.path', 'sentinel'),
    'middleware' => (array) config('sentinel.ui.middleware', ['web', 'auth']),
], static function (): void {
    Route::get('/', DashboardController::class)->name('sentinel.dashboard');
});
