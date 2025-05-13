<?php

namespace App\Models\Finance;

use App\Models\Plan;
use Laravel\Cashier\Subscription as CashierSubscription;

class Subscription extends CashierSubscription
{
    protected $table = 'subscriptions';

    public function plan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }
}
