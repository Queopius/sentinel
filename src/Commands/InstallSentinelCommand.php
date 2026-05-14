<?php

declare(strict_types=1);

namespace Queopius\Sentinel\Commands;

use Illuminate\Console\Command;

class InstallSentinelCommand extends Command
{
    protected $signature = 'sentinel:install {--with-views} {--force}';

    protected $description = 'Install Queopius Sentinel resources and show integration steps';

    public function handle(): int
    {
        $force = $this->option('force') ? ['--force' => true] : [];

        $this->call('vendor:publish', ['--tag' => 'sentinel-config', ...$force]);
        $this->call('vendor:publish', ['--tag' => 'sentinel-migrations', ...$force]);

        if ((bool) $this->option('with-views')) {
            $this->call('vendor:publish', ['--tag' => 'sentinel-views', ...$force]);
        }

        $this->info('Queopius Sentinel installed.');
        $this->line('Available publish tags: sentinel-config, sentinel-views, sentinel-migrations');
        $this->newLine();
        $this->line('Recommended rollout path:');
        $this->line('1) Set sentinel preset to web_compatible');
        $this->line('2) Keep CSP in report-only');
        $this->line('3) Review dashboard and CSP reports');
        $this->line('4) Harden directives and disable unsafe-inline');
        $this->line('5) Enable HSTS and HTTPS redirect in production');
        $this->newLine();
        $this->line('Next commands:');
        $this->line('- php artisan migrate');
        $this->line('- php artisan sentinel:audit');
        $this->line('- php artisan sentinel:scan');

        return self::SUCCESS;
    }
}
