<?php

declare(strict_types=1);

namespace Queopius\Sentinel\Contracts;

interface AuditFormatter
{
    /** @param array<string,mixed> $audit */
    public function format(array $audit): string;
}
