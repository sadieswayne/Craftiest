<?php

namespace App\Listeners;

use App\Events\IyzicoWebhookEvent;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserOrder;
use App\Models\WebhookHistory;
use App\Services\GatewaySelector;
use App\Services\PaymentGateways\Contracts\CreditUpdater;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use JsonException;
use Laravel\Cashier\Subscription as Subscriptions;
use Throwable;

class IyzicoWebhookListener implements ShouldQueue
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

    public int $delay = 0; // 60

    /**
     * Handle the event.
     */
    public function handle(IyzicoWebhookEvent $event): void
    {

        $newData = new WebhookHistory;

        try {
            $settings = Setting::getCache();
            $incomingJson = json_decode($event->payload, false, 512, JSON_THROW_ON_ERROR);
            // Incoming data is verified at IyzicoService handleWebhook function, which fires this event.
            $event_type = $incomingJson->iyziEventType;
            // save incoming data
            $newData->gatewaycode = 'iyzico';
            $newData->webhook_id = $incomingJson->iyziReferenceCode;
            $newData->create_time = $incomingJson->iyziEventTime;
            $newData->resource_type = 'subscription';
            $newData->event_type = $incomingJson->iyziEventType;
            $newData->summary = $incomingJson->orderReferenceCode;
            $newData->resource_id = $incomingJson->subscriptionReferenceCode;
            $newData->resource_state = $incomingJson->iyziEventType === 'subscription.order.success' ? 'paid' : 'cancelled';
            $newData->incoming_json = json_encode($incomingJson, JSON_THROW_ON_ERROR);
            $newData->status = 'check';
            $newData->save();
            if ($event_type === 'subscription.order.failure') {
                $currentSubscription = Subscriptions::where('stripe_id', $newData->resource_id)->first();
                if ($currentSubscription->stripe_status !== 'cancelled') {
                    $currentSubscription->stripe_status = 'cancelled';
                    $currentSubscription->ends_at = Carbon::now();
                    $currentSubscription->save();
                    $newData->status = 'checked';
                    $newData->save();
                }
            } elseif ($event_type === 'subscription.order.success') {
                // $resource_id is subscription id in this event.
                $currentSubscription = Subscriptions::where('stripe_id', $newData->resource_id)->first();
                $plan = Plan::find('id', $currentSubscription->plan_id);
                // check for duplication against time
                $duplicate = false;
                // check for first payment in subscription
                if (Carbon::parse($currentSubscription->created_at)->diffInMinutes(Carbon::parse($newData->create_time)) < 5) {
                    $duplicate = true;
                }
                // check current subscription status from iyzico
                if (GatewaySelector::selectGateway('iyzico')::getSubscriptionStatus() && ! $duplicate) {
                    // if it is trial then convert it to active
                    // if it is active and/or converted to active add plan word/image amount to the user
                    // if($currentSubscription->stripe_status == 'trialing'){} // it may be cancelled so in any case its going to be active
                    $currentSubscription->stripe_status = 'active';
                    $currentSubscription->save();

                    UserOrder::create([
                        'order_id'           => $incomingJson->orderReferenceCode,
                        'plan_id'            => $plan->id,
                        'user_id'            => $currentSubscription->user_id,
                        'payment_type'       => 'iyzico Recurring Payment',
                        'price'              => $plan->price,
                        'affiliate_earnings' => ($plan->price * $settings->affiliate_commission_percentage) / 100,
                        'status'             => 'Success',
                        'country'            => $user->country ?? 'Unknown',
                    ]);

                    $user = User::where('id', $currentSubscription->user_id)->first();
                    self::creditIncreaseSubscribePlan($user, $plan);
                    $newData->status = 'checked';
                    $newData->save();
                }
            }
        } catch (Exception $ex) {
            Log::error("IyzicoWebhookListener::handle()\n" . $ex->getMessage() . "\n" . $event->payload);
            $newData->status = 'error';
            $newData->save();
        }
    }

    /**
     * Handle a job failure.
     *
     * @throws JsonException
     */
    public function failed(IyzicoWebhookEvent $event, Throwable $exception): void
    {
        $space = '*****';
        $msg = '\n' . $space . '\n' . $space;
        $msg .= json_encode($event->payload, JSON_THROW_ON_ERROR);
        $msg .= '\n' . $space . '\n';
        $msg .= '\n' . $exception . '\n';
        $msg .= '\n' . $space . '\n' . $space;
        Log::error($msg);
    }
}
