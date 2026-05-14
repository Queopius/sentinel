<?php

declare(strict_types=1);

namespace Queopius\Sentinel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;
use Queopius\Sentinel\Events\SentinelAuditCompleted;
use Queopius\Sentinel\Support\SecurityAuditService;

class AuditSentinelCommand extends Command
{
    protected $signature = 'sentinel:audit {--format=table : table|json|csv}';

    protected $description = 'Run Queopius Sentinel security audit';

    public function handle(SecurityAuditService $auditService): int
    {
        $request = request();
        $result = $auditService->audit($request, (array) config('sentinel', []));

        Event::dispatch(new SentinelAuditCompleted($result));

        $format = (string) $this->option('format');
        if ($format === 'json') {
            $this->line((string) json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        }

        if ($format === 'csv') {
            $this->line('key,status,message');
            foreach ($result['checks'] as $check) {
                $this->line(sprintf('"%s","%s","%s"', $check['key'], $check['status'], str_replace('"', '""', $check['message'])));
            }

            return self::SUCCESS;
        }

        $this->table(['Check', 'Status', 'Message'], array_map(static fn (array $c): array => [$c['key'], $c['status'], $c['message']], $result['checks']));

        if ($result['warnings'] !== []) {
            $this->warn('Warnings:');
            foreach ($result['warnings'] as $warning) {
                $this->line('- '.$warning);
            }
        }

        return self::SUCCESS;
    }
}
