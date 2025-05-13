<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevenueCatProducts extends Model
{
    use HasFactory;

    protected $table = 'revenuecat_products';

    // / Return the gateway product of this revenuecat product defined as foreign key
    public function gateway_product()
    {
        return $this->belongsTo(GatewayProducts::class, 'gatewayproduct_id', 'id');
    }

    // / Return the plan of this revenuecat product defined as foreign key
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id', 'id');
    }
}
