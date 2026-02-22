<?php

declare(strict_types=1);

namespace Queopius\Shield\Events;

class ShieldScanCompleted
{
    /** @param array<int,array<string,mixed>> $results */
    public function __construct(public readonly array $results) {}
}
