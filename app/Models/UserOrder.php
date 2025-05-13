<?php

namespace App\Models;

use App\Helpers\Classes\MarketplaceHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Throwable;

class UserOrder extends Model
{
    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::created(static function ($model) {
            if ($model->plan && $model->plan['hidden'] && $model->plan['max_subscribe'] != -1 && $model->plan['max_subscribe'] != 0) {
                $model->plan['max_subscribe'] -= 1;
                $model->plan->save();
            }

            if (MarketplaceHelper::isRegistered('xero')) {
                try {
                    config([
                        'xero.clientId'     => setting('XERO_CLIENT_ID'),
                        'xero.clientSecret' => setting('XERO_CLIENT_SECRET'),
                        'xero.redirectUri'  => setting('XERO_REDIRECT_URI'),
                        'xero.landingUri'   => setting('XERO_LANDING_URL'),
                    ]);
                    $nowDate = date('Y-m-d');
                    $data = [
                        'Type'    => 'ACCREC',
                        'Contact' => [
                            'ContactID' => $model->user->xero_account_id,
                        ],
                        'Date'            => $nowDate,
                        'DueDate'         => $nowDate,
                        'LineAmountTypes' => 'Inclusive',
                        'LineItems'       => [
                            [
                                'Description' => $model->plan?->name,
                                'Quantity'    => 1,
                                'UnitAmount'  => $model->price,
                            ],
                        ],
                    ];

                    $response = \Dcblogdev\Xero\Facades\Xero::invoices()->store($data);
                } catch (Throwable $e) {
                }
            }
        });
    }
}
