<?php

declare(strict_types=1);

namespace Queopius\Shield\Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Queopius\Shield\Models\CspReport;
use Queopius\Shield\Tests\TestCase;

class CspReportsEndpointTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('shield_csp_reports')) {
            Schema::create('shield_csp_reports', function (Blueprint $table): void {
                $table->id();
                $table->json('payload')->nullable();
                $table->text('document_uri')->nullable();
                $table->text('blocked_uri')->nullable();
                $table->string('violated_directive')->nullable();
                $table->string('effective_directive')->nullable();
                $table->text('original_policy')->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('received_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function test_stores_valid_payload(): void
    {
        $payload = [
            'csp-report' => [
                'document-uri' => 'https://example.test/',
                'blocked-uri' => 'https://evil.test/x.js',
                'violated-directive' => 'script-src',
                'effective-directive' => 'script-src',
            ],
        ];

        $this->postJson('/shield/csp-reports', $payload)->assertNoContent();
        $this->assertDatabaseHas('shield_csp_reports', ['effective_directive' => 'script-src']);
    }

    public function test_tolerates_invalid_payload(): void
    {
        $this->postJson('/shield/csp-reports', [])->assertNoContent();
        $this->assertSame(0, CspReport::query()->count());
    }
}
