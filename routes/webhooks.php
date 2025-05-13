<?php

use App\Http\Controllers\Finance\PaymentProcessController;
use Illuminate\Support\Facades\Route;

Route::prefix('webhooks')
    ->name('webhooks.')
    ->group(function () {
        Route::match(['get', 'post'], '/{gateway}', [PaymentProcessController::class, 'handleWebhook']);

        Route::any('stripe/{subscription}/success', [PaymentProcessController::class, 'stripeSuccess'])->name('stripe.success');
        Route::any('stripe/{subscription}/cancel', [PaymentProcessController::class, 'stripeCancel'])->name('stripe.cancel');

        Route::any('stripe/{plan}/{user}/success/prepaid', [PaymentProcessController::class, 'prepaidStripeSuccess'])->name('stripe.success');
        Route::any('stripe/cancel/prepaid', [PaymentProcessController::class, 'stripeCancel'])->name('stripe.cancel');
    });
