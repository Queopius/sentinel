<?php

declare(strict_types=1);

namespace Queopius\Shield\Tests\Unit;

use Queopius\Shield\Support\HstsBuilder;
use Queopius\Shield\Tests\TestCase;

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
