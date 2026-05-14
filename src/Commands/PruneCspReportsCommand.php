<?php

declare(strict_types=1);

namespace Queopius\Sentinel\Commands;

use Illuminate\Console\Command;
use Queopius\Sentinel\Models\CspReport;

class PruneCspReportsCommand extends Command
{
    protected $signature = 'sentinel:prune-reports {--days=}';

    protected $description = 'Prune old CSP reports from database';

    public function handle(): int
    {
        $days = (int) ($this->option('days') ?: config('sentinel.csp_reports.prune_days', 30));
        $threshold = now()->subDays(max(1, $days));

        $deleted = CspReport::query()->where('received_at', '<', $threshold)->delete();

        $this->info("Pruned {$deleted} reports older than {$days} days.");

        return self::SUCCESS;
    }
}
