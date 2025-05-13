<?php

namespace App\Listeners;

use App\Actions\CreateActivity;
use App\Enums\Plan\FrequencyEnum;
use App\Events\FreePaymentEvent;
use App\Models\UserOrder;
use App\Services\PaymentGateways\Contracts\CreditUpdater;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Subscription as Subscriptions;

class FreePaymentListener implements ShouldQueue
{
    use CreditUpdater;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    use InteractsWithQueue;

    public bool $afterCommit = true;

    public ?string $queue = 'default';

    public int $delay = 0;

    /**
     * Handle the event.
     */
    public function handle(FreePaymentEvent $event): void
    {
        try {
            $status = $event->status;
            $order_ids = $event->orderIds;
            // 1. free_renewed, 2. free_expired
            if ($status === 'free_expired') {
                Subscriptions::whereIn('stripe_id', $order_ids)->update(['stripe_status' => $status]);
                $orders = UserOrder::whereIn('order_id', $order_ids)->get();
                foreach ($orders as $order) {
                    self::creditDecreaseCancelPlan($order->user, $order->plan);
                    CreateActivity::for($order->user, __('Subscription Expired'), $order->plan->name . ' ' . __('Plan'));
                }
            } elseif ($status === 'free_renewed') {
                $orders = UserOrder::whereIn('order_id', $order_ids)->get();
                foreach ($orders as $order) {
                    switch ($order->plan->frequency) {
                        case FrequencyEnum::LIFETIME_YEARLY->value:
                        case FrequencyEnum::YEARLY->value :
                            Subscriptions::where('stripe_id', $order->order_id)->update(['stripe_status' => $status, 'ends_at' => Carbon::now()->addYears(1)]);
                            $msg = __('Subscription renewed for 1 year.');

                            break;
                        default:
                            // FrequencyEnum::LIFETIME_MONTHLY->value
                            // FrequencyEnum::MONTHLY->value
                            Subscriptions::where('stripe_id', $order->order_id)->update(['stripe_status' => $status, 'ends_at' => Carbon::now()->addMonths(1)]);
                            $msg = __('Subscription renewed for 1 month.');

                            break;
                    }
                    self::creditIncreaseSubscribePlan($order->user, $order->plan);
                    CreateActivity::for($order->user, $msg, $order->plan->name . ' ' . __('Plan'));
                }
            }

        } catch (Exception $ex) {
            Log::error("FreeTransferListener::handle()\n" . $ex->getMessage());
        }
    }
}
