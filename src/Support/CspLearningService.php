<?php

declare(strict_types=1);

namespace Queopius\Sentinel\Support;

use Illuminate\Support\Collection;
use Queopius\Sentinel\Models\CspReport;

class CspLearningService
{
    /**
     * @return array<int, array{directive:string, host:string, hits:int}>
     */
    public function suggestions(int $limit = 20): array
    {
        if (! class_exists(CspReport::class) || ! $this->canQueryReports()) {
            return [];
        }

        /** @var Collection<int, CspReport> $rows */
        $rows = CspReport::query()->latest('received_at')->limit(1000)->get();

        $grouped = [];
        foreach ($rows as $row) {
            $directive = (string) ($row->effective_directive ?: $row->violated_directive ?: 'unknown');
            $host = $this->hostFromUri((string) $row->blocked_uri);
            $key = $directive.'|'.$host;
            $grouped[$key] = ($grouped[$key] ?? 0) + 1;
        }

        arsort($grouped);
        $result = [];
        foreach (array_slice($grouped, 0, $limit, true) as $key => $hits) {
            [$directive, $host] = explode('|', $key, 2);
            $result[] = ['directive' => $directive, 'host' => $host, 'hits' => $hits];
        }

        return $result;
    }

    private function hostFromUri(string $uri): string
    {
        $host = parse_url($uri, PHP_URL_HOST);

        return is_string($host) && $host !== '' ? $host : 'unknown';
    }

    private function canQueryReports(): bool
    {
        try {
            CspReport::query()->limit(1)->get();

            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
