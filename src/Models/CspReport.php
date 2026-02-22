<?php

declare(strict_types=1);

namespace Queopius\Shield\Models;

use Illuminate\Database\Eloquent\Model;

class CspReport extends Model
{
    protected $table = 'shield_csp_reports';

    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'received_at' => 'datetime',
    ];

    public $timestamps = true;
}
