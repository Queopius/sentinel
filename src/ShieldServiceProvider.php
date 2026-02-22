<?php

declare(strict_types=1);

namespace Queopius\Shield;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Queopius\Shield\Commands\AuditShieldCommand;
use Queopius\Shield\Commands\InstallShieldCommand;
use Queopius\Shield\Commands\PruneCspReportsCommand;
use Queopius\Shield\Commands\ScanShieldCommand;
use Queopius\Shield\Http\Middleware\AddSecurityHeaders;
use Queopius\Shield\Http\Middleware\EnforceHttps;
use Queopius\Shield\Support\CspBuilder;
use Queopius\Shield\Support\CspLearningService;
use Queopius\Shield\Support\EndpointScanner;
use Queopius\Shield\Support\HeaderInspector;
use Queopius\Shield\Support\HeaderManager;
use Queopius\Shield\Support\HstsBuilder;
use Queopius\Shield\Support\NonceManager;
use Queopius\Shield\Support\ProxyDetector;
use Queopius\Shield\Support\SecurityAuditService;
use Queopius\Shield\Support\ShieldPresetResolver;

class ShieldServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/shield.php', 'shield');

        $this->app->singleton(ShieldPresetResolver::class);
        $this->app->singleton(CspBuilder::class);
        $this->app->singleton(HstsBuilder::class);
        $this->app->singleton(NonceManager::class);
        $this->app->singleton(HeaderManager::class);
        $this->app->singleton(HeaderInspector::class);
        $this->app->singleton(ProxyDetector::class);
        $this->app->singleton(SecurityAuditService::class);
        $this->app->singleton(CspLearningService::class);
        $this->app->singleton(EndpointScanner::class);
    }

    public function boot(Router $router): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'shield');

        $this->publishes([
            __DIR__.'/../config/shield.php' => config_path('shield.php'),
        ], 'shield-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/shield'),
        ], 'shield-views');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'shield-migrations');

        $router->aliasMiddleware('shield.headers', AddSecurityHeaders::class);
        $router->aliasMiddleware('shield.https', EnforceHttps::class);

        if ((bool) config('shield.ui.enabled', false)) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }

        if ((bool) config('shield.csp_reports.enabled', false) || (bool) config('shield.health_endpoint.enabled', false)) {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallShieldCommand::class,
                AuditShieldCommand::class,
                ScanShieldCommand::class,
                PruneCspReportsCommand::class,
            ]);
        }
    }
}
