<?php

declare(strict_types=1);

namespace Queopius\Shield\Tests\Feature;

use Queopius\Shield\Tests\TestCase;

class CspEnforceTest extends TestCase
{
    public function test_emits_enforce_header(): void
    {
        config()->set('shield.headers.csp.enabled', true);
        config()->set('shield.headers.csp.report_only', false);

        $this->get('/probe')->assertHeader('Content-Security-Policy');
    }
}
