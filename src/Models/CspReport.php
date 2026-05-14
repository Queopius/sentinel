<?php

declare(strict_types=1);

namespace Queopius\Sentinel\Models;

use Illuminate\Database\Eloquent\Model;

class CspReport extends Model
{
    protected $table = 'sentinel_csp_reports';

    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'received_at' => 'datetime',
    ];

    public $timestamps = true;
}
