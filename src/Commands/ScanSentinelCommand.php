<?php

declare(strict_types=1);

namespace Queopius\Sentinel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;
use Queopius\Sentinel\Events\SentinelScanCompleted;
use Queopius\Sentinel\Support\EndpointScanner;

class ScanSentinelCommand extends Command
{
    protected $signature = 'sentinel:scan {--json} {--paths=/,/login,/api}';

    protected $description = 'Scan endpoints and compare security header consistency';

    public function handle(EndpointScanner $scanner): int
    {
        $paths = array_values(array_filter(array_map('trim', explode(',', (string) $this->option('paths')))));
        $results = $scanner->scan($paths, (array) config('sentinel', []));

        Event::dispatch(new SentinelScanCompleted($results));

        if ((bool) $this->option('json')) {
            $this->line((string) json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        }

        $rows = array_map(static fn (array $r): array => [
            $r['path'],
            (string) $r['status'],
            $r['ok'] ? 'yes' : 'no',
            (string) count((array) ($r['missing_headers'] ?? [])),
            (string) count((array) ($r['mismatched_headers'] ?? [])),
        ], $results);
        $this->table(['Path', 'Status', 'Headers OK', 'Missing', 'Mismatched'], $rows);

        return self::SUCCESS;
    }
}
