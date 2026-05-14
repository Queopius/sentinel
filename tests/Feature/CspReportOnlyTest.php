<?php

declare(strict_types=1);

namespace Queopius\Sentinel\Tests\Feature;

use Queopius\Sentinel\Tests\TestCase;

class CspReportOnlyTest extends TestCase
{
    public function test_emits_report_only_header(): void
    {
        config()->set('sentinel.headers.csp.enabled', true);
        config()->set('sentinel.headers.csp.report_only', true);

        $this->get('/probe')->assertHeader('Content-Security-Policy-Report-Only');
    }
}
