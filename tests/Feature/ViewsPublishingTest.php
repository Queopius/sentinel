<?php

declare(strict_types=1);

namespace Queopius\Sentinel\Tests\Feature;

use Queopius\Sentinel\Tests\TestCase;

class ViewsPublishingTest extends TestCase
{
    public function test_view_namespace_resolves(): void
    {
        $view = view('sentinel::dashboard', [
            'summary' => ['https_detected' => false, 'https_redirect_enabled' => false, 'hsts_applied' => false, 'csp_mode' => 'off', 'warnings_count' => 0],
            'checks' => [],
            'warnings' => [],
            'expectedHeaders' => [],
            'configSnapshot' => [],
            'learningSuggestions' => [],
            'showCspReports' => false,
            'recentReports' => [],
            'checkMetrics' => ['ok' => 0, 'warning' => 0, 'fail' => 0],
            'cspTimelineMetrics' => ['labels' => [], 'values' => []],
            'cspTopDirectivesMetrics' => ['labels' => [], 'values' => []],
            'selectedTimelineDays' => 30,
            'timelineOptions' => [7, 30, 90],
            'hardeningPlan' => [],
            'endpointScan' => [
                'enabled' => true,
                'selected_paths' => ['/'],
                'summary' => ['total' => 1, 'ok' => 1, 'with_missing' => 0, 'with_mismatched' => 0, 'total_missing_headers' => 0, 'total_mismatched_headers' => 0, 'average_score' => 100, 'worst_score' => 100],
                'rows' => [
                    ['path' => '/', 'status' => 200, 'ok' => true, 'missing_headers' => [], 'mismatched_headers' => [], 'expected_headers' => [], 'actual_headers' => [], 'score' => 100, 'severity' => 'low'],
                ],
            ],
            'selectedSection' => 'overview',
            'sections' => [
                'overview' => 'Overview',
                'headers' => 'Headers',
                'endpoints' => 'Endpoints',
                'reports' => 'Reports',
                'config' => 'Config',
            ],
        ])->render();

        $this->assertStringContainsString('Queopius Sentinel', $view);
    }
}
