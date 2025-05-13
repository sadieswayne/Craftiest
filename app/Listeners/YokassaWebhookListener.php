<?php

namespace App\Listeners;

use App\Events\StripeWebhookEvent;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserOrder;
use App\Models\WebhookHistory;
use App\Services\PaymentGateways\Contracts\CreditUpdater;
use App\Services\PaymentGateways\YokassaService;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use JsonException;
use Laravel\Cashier\Subscription as Subscriptions;
use Throwable;

class YokassaWebhookListener implements ShouldQueue
{
    use CreditUpdater;
    use InteractsWithQueue;

    public bool $afterCommit = true;

    public ?string $queue = 'default';

    public int $delay = 0;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(StripeWebhookEvent $event): void
    {
        try {
            $settings = Setting::getCache();
            $incomingJson = json_decode($event->payload, false, 512, JSON_THROW_ON_ERROR);

            // Incoming data is verified at StripeController handleWebhook function, which fires this event.

            $event_type = $incomingJson->type;
            // $resource_id = $incomingJson->data->object->lines->data[0]->price->id; //Price id
            if ($event_type === 'invoice.paid') {
                $resource_id = $incomingJson->data->object->subscription; // Subscription id
                $resource_type = $incomingJson->data->object->lines->data[0]->type; // Subscription / prepaid
                $summary = $incomingJson->data->object->lines->data[0]->description;
                $resource_state = $incomingJson->data->object->status;

            } elseif ($event_type === 'customer.subscription.deleted') {
                $resource_id = $incomingJson->data->object->items->data[0]->subscription; // Subscription id
                $resource_type = $incomingJson->data->object->object; // Subscription / prepaid
                $summary = $incomingJson->data->object->cancellation_details->reason;
                $resource_state = 'cancelled'; // $incomingJson->data->object->items->status;
            }

            // save incoming data
            $newData = new WebhookHistory;
            $newData->gatewaycode = 'stripe';
            $newData->webhook_id = $incomingJson->id;
            $newData->create_time = $incomingJson->created;
            $newData->resource_type = $resource_type; // Subscription / prepaid
            $newData->event_type = $event_type;
            $newData->summary = $summary;
            $newData->resource_id = $resource_id;
            $newData->resource_state = $resource_state;
            if ($event_type === 'invoice.paid') {
                $newData->parent_payment = $incomingJson->data->object->payment_intent;
                $newData->amount_total = $incomingJson->data->object->lines->data[0]->amount;
                $newData->amount_currency = $incomingJson->data->object->lines->data[0]->currency;
            }
            $newData->incoming_json = json_encode($incomingJson, JSON_THROW_ON_ERROR);
            $newData->status = 'check';
            $newData->save();
            // switch/check event type
            if ($event_type === 'customer.subscription.deleted') {
                // $resource_id is subscription id in this event.
                $currentSubscription = Subscriptions::where('stripe_id', $resource_id)->first();
                if ($currentSubscription->stripe_status !== 'cancelled') {
                    $currentSubscription->stripe_status = 'cancelled';
                    $currentSubscription->ends_at = Carbon::now();
                    $currentSubscription->save();
                    $newData->status = 'checked';
                    $newData->save();
                }
            } elseif ($event_type === 'invoice.paid') {
                // $resource_id is subscription id in this event.
                $activeSub = Subscriptions::where('stripe_id', $resource_id)->first();
                if (isset($activeSub->plan_id)) { // Plan may be deleted and null at database.
                    // Get plan
                    $plan = Plan::find($activeSub->plan_id);
                    if ($plan) {
                        // Check status from gateway first
                        $currentStripeStatus = YokassaService::getSubscriptionStatus($activeSub->user_id);
                        if ($currentStripeStatus) { // active or trial at stripe side
                            // check for duplication
                            $duplicate = false;
                            // check for first payment in subscription
                            if (Carbon::parse($activeSub->created_at)->diffInMinutes(Carbon::parse($incomingJson->created)) < 5) {
                                $duplicate = true;
                            }
                            if (! $duplicate) {
                                // if it is trial then convert it to active
                                // if it is active and/or converted to active add plan word/image amount to the user
                                // if($activeSub->stripe_status == 'trialing'){} // it may be cancelled so in any case its going to be active
                                $activeSub->stripe_status = 'active';
                                $activeSub->save();
                                UserOrder::create([
                                    'order_id'           => $incomingJson->id,
                                    'plan_id'            => $plan->id,
                                    'user_id'            => $activeSub->user_id,
                                    'payment_type'       => 'Stripe Recurring Payment',
                                    'price'              => $plan->price,
                                    'affiliate_earnings' => ($plan->price * $settings->affiliate_commission_percentage) / 100,
                                    'status'             => 'Success',
                                    'country'            => $user->country ?? 'Unknown',
                                ]);

                                $user = User::where('id', $activeSub->user_id)->first();
                                self::creditIncreaseSubscribePlan($user, $plan);
                                $newData->status = 'checked';
                                $newData->save();
                            }
                        }
                    }
                } else { // plan id is null at subscription database table.
                    if ($activeSub->stripe_status !== 'cancelled') {
                        $activeSub->stripe_status = 'cancelled';
                        $activeSub->ends_at = Carbon::now();
                        $activeSub->save();
                        $newData->status = 'checked';
                        $newData->save();
                    }
                    Log::error('Payment on a deleted plan. Please check: ' . $resource_id . ' with incoming webhook : ' . json_encode($incomingJson));
                }
            }
            // save new order if required
            // on cancel we do not delete anything. just check if subs cancelled
        } catch (Exception $ex) {
            Log::error("YokassaWebhookListener::handle()\n" . $ex->getMessage());
        }
    }

    /**
     * Handle a job failure.
     *
     * @throws JsonException
     */
    public function failed(StripeWebhookEvent $event, Throwable $exception): void
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
