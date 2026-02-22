<?php

declare(strict_types=1);

namespace Queopius\Shield\Events;

use Queopius\Shield\Models\CspReport;

class CspReportStored
{
    public function __construct(public readonly CspReport $report) {}
}
