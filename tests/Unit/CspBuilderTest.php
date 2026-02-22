<?php

declare(strict_types=1);

namespace Queopius\Shield\Tests\Unit;

use Queopius\Shield\Exceptions\InvalidShieldConfigException;
use Queopius\Shield\Support\CspBuilder;
use Queopius\Shield\Tests\TestCase;

class CspBuilderTest extends TestCase
{
    public function test_builds_directives_correctly(): void
    {
        $builder = new CspBuilder;
        $value = $builder->build([
            'directives' => [
                'default-src' => ["'self'"],
                'script-src' => ["'self'", 'https://cdn.example.com'],
            ],
        ]);

        $this->assertStringContainsString("default-src 'self'", $value);
        $this->assertStringContainsString("script-src 'self' https://cdn.example.com", $value);
    }

    public function test_supports_boolean_flags(): void
    {
        $builder = new CspBuilder;
        $value = $builder->build([
            'directives' => [
                'upgrade-insecure-requests' => true,
                'block-all-mixed-content' => true,
            ],
        ]);

        $this->assertStringContainsString('upgrade-insecure-requests', $value);
        $this->assertStringContainsString('block-all-mixed-content', $value);
    }

    public function test_ignores_empty_directives(): void
    {
        $builder = new CspBuilder;
        $value = $builder->build(['directives' => ['script-src' => []]]);

        $this->assertSame('', $value);
    }

    public function test_strict_validation_throws_for_invalid_directive(): void
    {
        $this->expectException(InvalidShieldConfigException::class);

        $builder = new CspBuilder;
        $builder->build(['directives' => ['bad-directive' => ["'self'"]]], null, true);
    }
}
