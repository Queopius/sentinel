<?php

declare(strict_types=1);

namespace Queopius\Sentinel\Events;

class SentinelScanCompleted
{
    /** @param array<int,array<string,mixed>> $results */
    public function __construct(public readonly array $results) {}
}
