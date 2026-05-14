<?php

declare(strict_types=1);

namespace Queopius\Sentinel\Support;

class HstsBuilder
{
    /** @param array<string,mixed> $config */
    public function build(array $config): string
    {
        $maxAge = (int) ($config['max_age'] ?? 0);
        $parts = ["max-age={$maxAge}"];

        if ((bool) ($config['include_subdomains'] ?? false)) {
            $parts[] = 'includeSubDomains';
        }

        if ((bool) ($config['preload'] ?? false)) {
            $parts[] = 'preload';
        }

        return implode('; ', $parts);
    }
}
