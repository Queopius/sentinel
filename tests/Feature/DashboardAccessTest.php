<?php

declare(strict_types=1);

namespace Queopius\Shield\Tests\Feature;

use Illuminate\Support\Facades\Gate;
use Queopius\Shield\Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    public function test_dashboard_route_works_when_enabled(): void
    {
        $this->get('/shield')->assertOk()->assertSee('Queopius Shield');
    }

    public function test_dashboard_forbidden_when_ability_denied(): void
    {
        config()->set('shield.ui.require_ability', 'viewShieldDashboard');

        Gate::define('viewShieldDashboard', static fn (): bool => false);

        $this->get('/shield')->assertForbidden();
    }

    public function test_dashboard_exports_endpoint_scan_as_json(): void
    {
        $this->get('/shield?export=endpoints&format=json')
            ->assertOk()
            ->assertHeader('content-type', 'application/json')
            ->assertJsonStructure(['generated_at', 'summary', 'rows']);
    }

    public function test_dashboard_exports_endpoint_scan_as_csv(): void
    {
        $response = $this->get('/shield?export=endpoints&format=csv');
        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString('path,status,ok,score,severity,missing_count,mismatched_count,missing_headers,mismatched_headers', $response->streamedContent());
    }
}
