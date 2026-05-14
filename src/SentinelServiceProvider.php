<?php

declare(strict_types=1);

namespace Queopius\Sentinel;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Queopius\Sentinel\Commands\AuditSentinelCommand;
use Queopius\Sentinel\Commands\InstallSentinelCommand;
use Queopius\Sentinel\Commands\PruneCspReportsCommand;
use Queopius\Sentinel\Commands\ScanSentinelCommand;
use Queopius\Sentinel\Http\Middleware\AddSecurityHeaders;
use Queopius\Sentinel\Http\Middleware\EnforceHttps;
use Queopius\Sentinel\Support\CspBuilder;
use Queopius\Sentinel\Support\CspLearningService;
use Queopius\Sentinel\Support\EndpointScanner;
use Queopius\Sentinel\Support\HeaderInspector;
use Queopius\Sentinel\Support\HeaderManager;
use Queopius\Sentinel\Support\HstsBuilder;
use Queopius\Sentinel\Support\NonceManager;
use Queopius\Sentinel\Support\ProxyDetector;
use Queopius\Sentinel\Support\SecurityAuditService;
use Queopius\Sentinel\Support\SentinelPresetResolver;

class SentinelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/sentinel.php', 'sentinel');

        $this->app->singleton(SentinelPresetResolver::class);
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
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'sentinel');

        $this->publishes([
            __DIR__.'/../config/sentinel.php' => config_path('sentinel.php'),
        ], 'sentinel-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/sentinel'),
        ], 'sentinel-views');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'sentinel-migrations');

        $router->aliasMiddleware('sentinel.headers', AddSecurityHeaders::class);
        $router->aliasMiddleware('sentinel.https', EnforceHttps::class);

        if ((bool) config('sentinel.ui.enabled', false)) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }

        if ((bool) config('sentinel.csp_reports.enabled', false) || (bool) config('sentinel.health_endpoint.enabled', false)) {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallSentinelCommand::class,
                AuditSentinelCommand::class,
                ScanSentinelCommand::class,
                PruneCspReportsCommand::class,
            ]);
        }
    }
}
