<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Queopius\Shield\Http\Controllers\DashboardController;

Route::group([
    'prefix' => (string) config('shield.ui.path', 'shield'),
    'middleware' => (array) config('shield.ui.middleware', ['web', 'auth']),
], static function (): void {
    Route::get('/', DashboardController::class)->name('shield.dashboard');
});
