<?php

declare(strict_types=1);

namespace Queopius\Sentinel\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string|null $blocked_uri
 * @property string|null $effective_directive
 * @property string|null $violated_directive
 */
class CspReport extends Model
{
    protected $table = 'sentinel_csp_reports';

    protected $guarded = [];

    /** @var array<string,string> */
    protected $casts = [
        'payload' => 'array',
        'received_at' => 'datetime',
    ];

    public $timestamps = true;
}
