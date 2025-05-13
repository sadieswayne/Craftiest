<?php

namespace App\Console\Commands;

// use App\Models\Subscriptions;
use App\Events\BankTransferEvent;
use App\Events\FreePaymentEvent;
use App\Events\IyzicoLifetimeEvent;
use App\Events\PaypalLifetimeEvent;
use App\Events\PaystackLifetimeEvent;
use App\Events\StripeLifetimeEvent;
use Illuminate\Console\Command;
use Laravel\Cashier\Subscription as Subscriptions;

class CheckSubscriptionEnd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:check-end';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check subscription end dates and take appropriate actions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();
        $allSubscriptions = Subscriptions::where(function ($query) use ($now) {
            $query->where('stripe_status', 'bank_approved')
                ->where('ends_at', '<=', $now);
        })->orWhere(function ($query) use ($now) {
            $query->where('stripe_status', 'bank_renewed')
                ->where('ends_at', '<=', $now);
        })->orWhere(function ($query) use ($now) {
            $query->where('stripe_status', 'free_approved')
                ->where('ends_at', '<=', $now);
        })
            ->orWhere(function ($query) use ($now) {
                $query->where('stripe_status', 'stripe_approved')
                    ->where('ends_at', '<=', $now);
            })->orWhere(function ($query) use ($now) {
                $query->where('stripe_status', 'paypal_approved')
                    ->where('ends_at', '<=', $now);
            })->orWhere(function ($query) use ($now) {
                $query->where('stripe_status', 'iyzico_approved')
                    ->where('ends_at', '<=', $now);
            })->orWhere(function ($query) use ($now) {
                $query->where('stripe_status', 'paystack_approved')
                    ->where('ends_at', '<=', $now);
            })->get();

        // free
        $cancelFreeSubscriptions = $allSubscriptions
            ->where('stripe_status', 'free_approved')
            ->where('auto_renewal', false);
        $renewFreeSubscriptions = $allSubscriptions
            ->where('stripe_status', 'free_approved')
            ->where('auto_renewal', true);
        $cancelFreeSubscriptionIds = $cancelFreeSubscriptions->pluck('stripe_id')->toArray();
        $renewFreeSubscriptionIds = $renewFreeSubscriptions->pluck('stripe_id')->toArray();
        if (! empty($cancelFreeSubscriptionIds)) {
            event(new FreePaymentEvent('free_expired', $cancelFreeSubscriptionIds));
        }
        if (! empty($renewFreeSubscriptionIds)) {
            event(new FreePaymentEvent('free_renewed', $renewFreeSubscriptionIds));
        }
        // bank
        $cancelBankSubscriptions = $allSubscriptions
            ->where('auto_renewal', false)
            ->where(function ($query) {
                $query->where('stripe_status', 'bank_approved')
                    ->orWhere('stripe_status', 'bank_renewed');
            });
        $renewBankSubscriptions = $allSubscriptions
            ->where('auto_renewal', true)
            ->where(function ($query) {
                $query->where('stripe_status', 'bank_approved')
                    ->orWhere('stripe_status', 'bank_renewed');
            });
        $renewBankSubscriptionIds = $renewBankSubscriptions->pluck('stripe_id')->toArray();
        $cancelBankSubscriptionIds = $cancelBankSubscriptions->pluck('stripe_id')->toArray();
        if (! empty($cancelBankSubscriptionIds)) {
            event(new BankTransferEvent('bank_expired', $cancelBankSubscriptionIds));
        }
        if (! empty($renewBankSubscriptionIds)) {
            event(new BankTransferEvent('bank_renewed', $renewBankSubscriptionIds));
        }
        // strip
        $renewStripeLifeTimeSubscriptions = $allSubscriptions
            ->where('stripe_status', 'stripe_approved')
            ->where('auto_renewal', true);
        $renewStripeLiftimeSubscriptionIds = $renewStripeLifeTimeSubscriptions->pluck('stripe_id')->toArray();
        if (! empty($renewStripeLiftimeSubscriptionIds)) {
            event(new StripeLifetimeEvent('stripe_approved', $renewStripeLiftimeSubscriptionIds));
        }
        // paypal
        $renewPaypalLifeTimeSubscriptions = $allSubscriptions
            ->where('stripe_status', 'paypal_approved')
            ->where('auto_renewal', true);
        $renewPaypalLiftimeSubscriptionIds = $renewPaypalLifeTimeSubscriptions->pluck('stripe_id')->toArray();
        if (! empty($renewPaypalLiftimeSubscriptionIds)) {
            event(new PaypalLifetimeEvent('paypal_approved', $renewPaypalLiftimeSubscriptionIds));
        }
        // iyzico
        $renewIyzicoLifeTimeSubscriptions = $allSubscriptions
            ->where('stripe_status', 'iyzico_approved')
            ->where('auto_renewal', true);
        $renewIyzicoLiftimeSubscriptionIds = $renewIyzicoLifeTimeSubscriptions->pluck('stripe_id')->toArray();
        if (! empty($renewIyzicoLiftimeSubscriptionIds)) {
            event(new IyzicoLifetimeEvent('iyzico_approved', $renewIyzicoLiftimeSubscriptionIds));
        }
        // paystack
        $renewPaystackLifeTimeSubscriptions = $allSubscriptions
            ->where('stripe_status', 'iyzico_approved')
            ->where('auto_renewal', true);
        $renewPaystackLiftimeSubscriptionIds = $renewPaystackLifeTimeSubscriptions->pluck('stripe_id')->toArray();
        if (! empty($renewPaystackLiftimeSubscriptionIds)) {
            event(new PaystackLifetimeEvent('paystack_approved', $renewPaystackLiftimeSubscriptionIds));
        }

        $this->info('Subscription check event run successfully.');
    }
}
