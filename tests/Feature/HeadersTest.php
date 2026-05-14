<?php

declare(strict_types=1);

namespace Queopius\Sentinel\Tests\Feature;

use Queopius\Sentinel\Tests\TestCase;

class HeadersTest extends TestCase
{
    public function test_applies_common_headers(): void
    {
        config()->set('sentinel.enabled', true);

        $this->get('/probe')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy')
            ->assertHeader('Permissions-Policy');
    }
}
