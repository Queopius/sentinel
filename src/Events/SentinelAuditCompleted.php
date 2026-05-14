<?php

declare(strict_types=1);

namespace Queopius\Sentinel\Events;

class SentinelAuditCompleted
{
    /** @param array<string,mixed> $result */
    public function __construct(public readonly array $result) {}
}
