<?php

declare(strict_types=1);

namespace Queopius\Sentinel\Events;

use Queopius\Sentinel\Models\CspReport;

class CspReportStored
{
    public function __construct(public readonly CspReport $report) {}
}
