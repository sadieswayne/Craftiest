<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GatewayProducts extends Model
{
    protected $table = 'gatewayproducts';

    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
    ];

    public function revenuecat_products(): HasMany
    {
        return $this->hasMany(RevenueCatProducts::class, 'gatewayproduct_id', 'id');
    }
}
