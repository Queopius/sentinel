<?php

declare(strict_types=1);

namespace Queopius\Sentinel\ViewModels;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Queopius\Sentinel\Models\CspReport;
use Queopius\Sentinel\Support\CspLearningService;
use Queopius\Sentinel\Support\EndpointScanner;
use Queopius\Sentinel\Support\SecurityAuditService;

class SentinelDashboardViewModel
{
    public function __construct(
        private readonly SecurityAuditService $auditService,
        private readonly CspLearningService $learningService,
        private readonly EndpointScanner $endpointScanner,
    ) {}

    /** @return array<string,mixed> */
    public function data(Request $request): array
    {
        $config = (array) config('sentinel', []);
        $audit = $this->auditService->audit($request, $config);
        $timelineDays = $this->resolveTimelineDays($request);
        $checkMetrics = $this->buildCheckMetrics($audit['checks']);
        $cspTimeline = $this->buildCspTimelineMetrics($timelineDays);
        $cspTopDirectives = $this->buildCspTopDirectivesMetrics(8, $timelineDays);
        $hardeningPlan = $this->buildHardeningPlan($audit);
        $endpointScan = $this->buildEndpointScanData($request, $config);
        $selectedSection = $this->resolveSection($request);
        $uiTheme = $this->resolveTheme($request, $config);

        return [
            'summary' => $audit['summary'],
            'checks' => $audit['checks'],
            'warnings' => $audit['warnings'],
            'expectedHeaders' => $audit['expected_headers'],
            'configSnapshot' => $this->configSnapshot($config),
            'learningSuggestions' => $this->learningService->suggestions(12),
            'showCspReports' => (bool) data_get($config, 'ui.show_csp_reports', false),
            'checkMetrics' => $checkMetrics,
            'cspTimelineMetrics' => $cspTimeline,
            'cspTopDirectivesMetrics' => $cspTopDirectives,
            'selectedTimelineDays' => $timelineDays,
            'timelineOptions' => [7, 30, 90],
            'hardeningPlan' => $hardeningPlan,
            'endpointScan' => $endpointScan,
            'selectedSection' => $selectedSection,
            'sections' => [
                'overview' => 'Overview',
                'headers' => 'Headers',
                'endpoints' => 'Endpoints',
                'reports' => 'Reports',
                'config' => 'Config',
            ],
            'uiTheme' => $uiTheme,
        ];
    }

    /** @param array<string,mixed> $config @return array<string,mixed> */
    private function configSnapshot(array $config): array
    {
        return [
            'enabled' => (bool) data_get($config, 'enabled', false),
            'preset' => data_get($config, 'preset'),
            'https_redirect' => (bool) data_get($config, 'https.redirect', false),
            'force_scheme' => (bool) data_get($config, 'https.force_scheme', false),
            'csp_enabled' => (bool) data_get($config, 'headers.csp.enabled', false),
            'csp_report_only' => (bool) data_get($config, 'headers.csp.report_only', false),
            'hsts_enabled' => (bool) data_get($config, 'headers.hsts.enabled', false),
            'ui_enabled' => (bool) data_get($config, 'ui.enabled', false),
            'csp_reports_enabled' => (bool) data_get($config, 'csp_reports.enabled', false),
        ];
    }

    /**
     * @param  array<int, array{key:string,status:string,message:string}>  $checks
     * @return array{ok:int,warning:int,fail:int}
     */
    private function buildCheckMetrics(array $checks): array
    {
        $metrics = ['ok' => 0, 'warning' => 0, 'fail' => 0];

        foreach ($checks as $check) {
            $status = (string) ($check['status'] ?? '');
            if (array_key_exists($status, $metrics)) {
                $metrics[$status]++;
            }
        }

        return $metrics;
    }

    /**
     * @return array{labels: array<int,string>, values: array<int,int>}
     */
    private function buildCspTimelineMetrics(int $days): array
    {
        $start = CarbonImmutable::now()->subDays($days - 1)->startOfDay();
        $labels = [];
        $values = [];

        for ($i = 0; $i < $days; $i++) {
            $labels[] = $start->addDays($i)->format('Y-m-d');
            $values[] = 0;
        }

        if (! $this->canQueryReports()) {
            return ['labels' => $labels, 'values' => $values];
        }

        $rows = CspReport::query()
            ->selectRaw('DATE(received_at) as report_day, COUNT(*) as total')
            ->where('received_at', '>=', $start)
            ->groupBy('report_day')
            ->pluck('total', 'report_day')
            ->toArray();

        foreach ($labels as $index => $day) {
            $values[$index] = (int) ($rows[$day] ?? 0);
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * @return array{labels: array<int,string>, values: array<int,int>}
     */
    private function buildCspTopDirectivesMetrics(int $limit, int $days): array
    {
        if (! $this->canQueryReports()) {
            return ['labels' => [], 'values' => []];
        }

        $start = CarbonImmutable::now()->subDays($days - 1)->startOfDay();

        $rows = CspReport::query()
            ->selectRaw("COALESCE(NULLIF(effective_directive, ''), NULLIF(violated_directive, ''), 'unknown') as directive_key, COUNT(*) as total")
            ->where('received_at', '>=', $start)
            ->groupBy('directive_key')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();

        $labels = [];
        $values = [];
        foreach ($rows as $row) {
            $labels[] = (string) $row->directive_key;
            $values[] = (int) $row->total;
        }

        return ['labels' => $labels, 'values' => $values];
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

    private function resolveTimelineDays(Request $request): int
    {
        $allowed = [7, 30, 90];
        $days = (int) $request->integer('days', 30);

        return in_array($days, $allowed, true) ? $days : 30;
    }

    private function resolveSection(Request $request): string
    {
        $allowed = ['overview', 'headers', 'endpoints', 'reports', 'config'];
        $section = (string) $request->query('section', 'overview');

        return in_array($section, $allowed, true) ? $section : 'overview';
    }

    /**
     * Returns computed UI theme. `auto` is mapped to `light` for predictable server-side rendering.
     *
     * @param  array<string,mixed>  $config
     */
    private function resolveTheme(Request $request, array $config): string
    {
        $allowed = ['light', 'dark', 'auto'];
        $rawTheme = (string) $request->query('theme', (string) data_get($config, 'ui.theme', 'light'));
        $theme = in_array($rawTheme, $allowed, true) ? $rawTheme : 'light';

        return $theme === 'auto' ? 'light' : $theme;
    }

    /**
     * @param  array<string,mixed>  $config
     * @return array{
     *   enabled: bool,
     *   selected_paths: array<int,string>,
     *   summary: array{total:int,ok:int,with_missing:int,with_mismatched:int,total_missing_headers:int,total_mismatched_headers:int,average_score:int,worst_score:int},
     *   rows: array<int, array{
     *     path:string,
     *     status:int,
     *     ok:bool,
     *     missing_headers:array<int,string>,
     *     mismatched_headers:array<int,array{header:string,expected:string,actual:string}>,
     *     expected_headers:array<string,string>,
     *     actual_headers:array<string,string>,
     *     score:int,
     *     severity:string
     *   }>
     * }
     */
    private function buildEndpointScanData(Request $request, array $config): array
    {
        $enabled = (bool) data_get($config, 'ui.endpoint_scan.enabled', true);
        if (! $enabled) {
            return [
                'enabled' => false,
                'selected_paths' => [],
                'summary' => ['total' => 0, 'ok' => 0, 'with_missing' => 0, 'with_mismatched' => 0, 'total_missing_headers' => 0, 'total_mismatched_headers' => 0, 'average_score' => 100, 'worst_score' => 100],
                'rows' => [],
            ];
        }

        $rawPaths = trim((string) $request->query('scan_paths', ''));
        if ($rawPaths !== '') {
            $paths = array_values(array_unique(array_filter(array_map(
                static fn (string $path): string => trim($path),
                preg_split('/[\r\n,]+/', $rawPaths) ?: []
            ))));
        } else {
            $paths = array_values(array_unique(array_filter(array_map(
                static fn (mixed $path): string => trim((string) $path),
                (array) data_get($config, 'ui.endpoint_scan.paths', ['/'])
            ))));
        }

        $currentPath = '/'.ltrim($request->path(), '/');
        if ($currentPath === '//') {
            $currentPath = '/';
        }

        $paths = array_values(array_filter($paths, static fn (string $path): bool => '/'.ltrim($path, '/') !== $currentPath));
        $maxPaths = max(1, (int) data_get($config, 'ui.endpoint_scan.max_paths', 8));
        $paths = array_slice($paths, 0, $maxPaths);

        if ($paths === []) {
            return [
                'enabled' => true,
                'selected_paths' => [],
                'summary' => ['total' => 0, 'ok' => 0, 'with_missing' => 0, 'with_mismatched' => 0, 'total_missing_headers' => 0, 'total_mismatched_headers' => 0, 'average_score' => 100, 'worst_score' => 100],
                'rows' => [],
            ];
        }

        $rows = array_map(fn (array $row): array => $this->enrichEndpointRisk($row), $this->endpointScanner->scan($paths, $config));
        $ok = 0;
        $withMissing = 0;
        $withMismatched = 0;
        $totalMissing = 0;
        $totalMismatched = 0;
        $scoreSum = 0;
        $worstScore = 100;
        foreach ($rows as $row) {
            if ((bool) ($row['ok'] ?? false)) {
                $ok++;
            }

            $missingCount = count((array) ($row['missing_headers'] ?? []));
            $mismatchedCount = count((array) ($row['mismatched_headers'] ?? []));
            if ($missingCount > 0) {
                $withMissing++;
            }
            if ($mismatchedCount > 0) {
                $withMismatched++;
            }

            $totalMissing += $missingCount;
            $totalMismatched += $mismatchedCount;
            $score = (int) ($row['score'] ?? 100);
            $scoreSum += $score;
            $worstScore = min($worstScore, $score);
        }
        $totalRows = count($rows);
        $averageScore = $totalRows > 0 ? (int) round($scoreSum / $totalRows) : 100;

        return [
            'enabled' => true,
            'selected_paths' => $paths,
            'summary' => [
                'total' => $totalRows,
                'ok' => $ok,
                'with_missing' => $withMissing,
                'with_mismatched' => $withMismatched,
                'total_missing_headers' => $totalMissing,
                'total_mismatched_headers' => $totalMismatched,
                'average_score' => $averageScore,
                'worst_score' => $worstScore,
            ],
            'rows' => $rows,
        ];
    }

    /**
     * @param array{
     *   path:string,
     *   status:int,
     *   ok:bool,
     *   missing_headers:array<int,string>,
     *   mismatched_headers:array<int,array{header:string,expected:string,actual:string}>,
     *   expected_headers:array<string,string>,
     *   actual_headers:array<string,string>
     * } $row
     * @return array{
     *   path:string,
     *   status:int,
     *   ok:bool,
     *   missing_headers:array<int,string>,
     *   mismatched_headers:array<int,array{header:string,expected:string,actual:string}>,
     *   expected_headers:array<string,string>,
     *   actual_headers:array<string,string>,
     *   score:int,
     *   severity:string
     * }
     */
    private function enrichEndpointRisk(array $row): array
    {
        $criticalHeaders = [
            'Strict-Transport-Security',
            'Content-Security-Policy',
            'Content-Security-Policy-Report-Only',
            'X-Content-Type-Options',
            'Referrer-Policy',
            'X-Frame-Options',
            'Permissions-Policy',
            'Cross-Origin-Opener-Policy',
            'Cross-Origin-Resource-Policy',
        ];

        $score = 100;
        $missing = (array) ($row['missing_headers'] ?? []);
        $mismatched = (array) ($row['mismatched_headers'] ?? []);
        $status = (int) ($row['status'] ?? 200);

        foreach ($missing as $header) {
            $score -= in_array($header, $criticalHeaders, true) ? 14 : 9;
        }

        foreach ($mismatched as $item) {
            $header = (string) ($item['header'] ?? '');
            $score -= in_array($header, $criticalHeaders, true) ? 10 : 6;
        }

        if ($status >= 500) {
            $score -= 20;
        } elseif ($status >= 400) {
            $score -= 10;
        }

        $score = max(0, min(100, $score));

        $severity = 'low';
        if ($score < 40) {
            $severity = 'critical';
        } elseif ($score < 70) {
            $severity = 'high';
        } elseif ($score < 90) {
            $severity = 'medium';
        }

        $row['score'] = $score;
        $row['severity'] = $severity;

        return $row;
    }

    /**
     * @param  array{checks: array<int, array{key:string,status:string,message:string}>, warnings: array<int,string>, summary: array<string,mixed>, expected_headers: array<string,string>}  $audit
     * @return array<int, array{priority:string,title:string,description:string}>
     */
    private function buildHardeningPlan(array $audit): array
    {
        $plan = [];
        $summary = (array) ($audit['summary'] ?? []);
        $warnings = (array) ($audit['warnings'] ?? []);

        if (! (bool) ($summary['https_redirect_enabled'] ?? false)) {
            $plan[] = [
                'priority' => 'high',
                'title' => 'Enable HTTPS redirect',
                'description' => 'Turn on `sentinel.https.redirect` and keep redirects at edge/load balancer as primary enforcement.',
            ];
        }

        if ((string) ($summary['csp_mode'] ?? 'off') === 'off') {
            $plan[] = [
                'priority' => 'high',
                'title' => 'Enable CSP in report-only',
                'description' => 'Start in report-only mode, collect violations, then move to enforce after tuning sources.',
            ];
        } elseif ((string) ($summary['csp_mode'] ?? '') === 'report-only') {
            $plan[] = [
                'priority' => 'medium',
                'title' => 'Promote CSP to enforce mode',
                'description' => 'After at least a few days of clean reports, disable report-only in production.',
            ];
        }

        if (! (bool) ($summary['hsts_applied'] ?? false)) {
            $plan[] = [
                'priority' => 'medium',
                'title' => 'Apply HSTS on HTTPS requests',
                'description' => 'Verify TLS termination/proxy trust so Sentinel can emit Strict-Transport-Security.',
            ];
        }

        foreach ($warnings as $warning) {
            $text = (string) $warning;
            if (str_contains($text, "'unsafe-inline'")) {
                $plan[] = [
                    'priority' => 'medium',
                    'title' => 'Remove unsafe-inline from script-src',
                    'description' => 'Replace inline scripts with external files or use CSP nonces for controlled inline code.',
                ];
            }
            if (str_contains($text, 'Permissions-Policy')) {
                $plan[] = [
                    'priority' => 'low',
                    'title' => 'Enable Permissions-Policy',
                    'description' => 'Set an explicit policy for camera, microphone, geolocation and other sensitive APIs.',
                ];
            }
        }

        return array_values(array_unique($plan, SORT_REGULAR));
    }
}
