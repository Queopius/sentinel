<?php

declare(strict_types=1);

namespace Queopius\Sentinel\Tests\Feature;

use Queopius\Sentinel\Tests\TestCase;

class CspEnforceTest extends TestCase
{
    public function test_emits_enforce_header(): void
    {
        config()->set('sentinel.headers.csp.enabled', true);
        config()->set('sentinel.headers.csp.report_only', false);

        $this->get('/probe')->assertHeader('Content-Security-Policy');
    }
}
