<?php

declare(strict_types=1);

namespace Queopius\Shield\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as Orchestra;
use Queopius\Shield\Http\Middleware\AddSecurityHeaders;
use Queopius\Shield\Http\Middleware\EnforceHttps;
use Queopius\Shield\ShieldServiceProvider;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function getPackageProviders($app): array
    {
        return [ShieldServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $shield = require __DIR__.'/../config/shield.php';
        $shield['ui']['enabled'] = true;
        $shield['ui']['middleware'] = ['web'];
        $shield['csp_reports']['enabled'] = true;
        $app['config']->set('shield', $shield);
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadLaravelMigrations();

        $this->app['db']->connection()->getSchemaBuilder()->create('shield_csp_reports', function (Blueprint $table): void {
            $table->id();
            $table->json('payload')->nullable();
            $table->text('document_uri')->nullable();
            $table->text('blocked_uri')->nullable();
            $table->string('violated_directive')->nullable();
            $table->string('effective_directive')->nullable();
            $table->text('original_policy')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
        });
    }

    protected function defineRoutes($router): void
    {
        Route::middleware([AddSecurityHeaders::class])->get('/probe', fn () => response('ok'));
        Route::middleware([EnforceHttps::class])->get('/force-https', fn () => response('ok'));
        Route::get('/excluded', fn () => response('ok'))->name('excluded.route');
    }
}
