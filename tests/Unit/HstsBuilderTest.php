<?php

declare(strict_types=1);

namespace Queopius\Sentinel\Tests\Unit;

use Queopius\Sentinel\Support\HstsBuilder;
use Queopius\Sentinel\Tests\TestCase;

class HstsBuilderTest extends TestCase
{
    public function test_composes_hsts_value(): void
    {
        $builder = new HstsBuilder;
        $value = $builder->build([
            'max_age' => 31536000,
            'include_subdomains' => true,
            'preload' => true,
        ]);

        $this->assertSame('max-age=31536000; includeSubDomains; preload', $value);
    }
}
