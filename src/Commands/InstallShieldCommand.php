<?php

declare(strict_types=1);

namespace Queopius\Shield\Commands;

use Illuminate\Console\Command;

class InstallShieldCommand extends Command
{
    protected $signature = 'shield:install {--with-views} {--force}';

    protected $description = 'Install Queopius Shield resources and show integration steps';

    public function handle(): int
    {
        $force = $this->option('force') ? ['--force' => true] : [];

        $this->call('vendor:publish', ['--tag' => 'shield-config', ...$force]);
        $this->call('vendor:publish', ['--tag' => 'shield-migrations', ...$force]);

        if ((bool) $this->option('with-views')) {
            $this->call('vendor:publish', ['--tag' => 'shield-views', ...$force]);
        }

        $this->info('Queopius Shield installed.');
        $this->line('Available publish tags: shield-config, shield-views, shield-migrations');
        $this->newLine();
        $this->line('Recommended rollout path:');
        $this->line('1) Set shield preset to web_compatible');
        $this->line('2) Keep CSP in report-only');
        $this->line('3) Review dashboard and CSP reports');
        $this->line('4) Harden directives and disable unsafe-inline');
        $this->line('5) Enable HSTS and HTTPS redirect in production');
        $this->newLine();
        $this->line('Next commands:');
        $this->line('- php artisan migrate');
        $this->line('- php artisan shield:audit');
        $this->line('- php artisan shield:scan');

        return self::SUCCESS;
    }
}
