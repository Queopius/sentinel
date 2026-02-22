<?php

declare(strict_types=1);

namespace Queopius\Shield\Events;

class ShieldAuditCompleted
{
    /** @param array<string,mixed> $result */
    public function __construct(public readonly array $result) {}
}
