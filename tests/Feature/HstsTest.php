<?php

declare(strict_types=1);

namespace Queopius\Shield\Tests\Feature;

use Queopius\Shield\Tests\TestCase;

class HstsTest extends TestCase
{
    public function test_applies_hsts_on_https_request(): void
    {
        $this->get('https://localhost/probe')
            ->assertHeader('Strict-Transport-Security');
    }

    public function test_does_not_apply_hsts_on_http_request(): void
    {
        $this->get('/probe')->assertHeaderMissing('Strict-Transport-Security');
    }
}
