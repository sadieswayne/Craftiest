<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GatewayTax extends Model
{
    protected $fillable = [
        'gateway_id',
        'country_code',
        'tax',
    ];
}
