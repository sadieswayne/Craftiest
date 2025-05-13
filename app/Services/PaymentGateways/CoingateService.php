<?php

namespace App\Services\PaymentGateways;

use App\Actions\CreateActivity;
use App\Actions\EmailPaymentConfirmation;
use App\Enums\Plan\FrequencyEnum;
use App\Helpers\Classes\Helper;
use App\Models\GatewayProducts;
use App\Models\Gateways;
use App\Models\OldGatewayProducts;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserOrder;
use App\Services\Contracts\BaseGatewayService;
use App\Services\PaymentGateways\Contracts\CreditUpdater;
use Arr;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Cashier\Subscription as Subscriptions;

class CoingateService implements BaseGatewayService
{
    use CreditUpdater;

    public static $gateway;

    protected static string $GATEWAY_CODE = 'coingate';

    protected static string $GATEWAY_NAME = 'Coingate';

    public static function saveAllProducts()
    {
        try {
            $gateway = self::geteway();

            if ($gateway == null) {
                return back()->with(['message' => __('Please enable coingate'), 'type' => 'error']);
            }

            $plans = Plan::query()->where('active', 1)->get();

            foreach ($plans as $plan) {
                self::saveProduct($plan);
            }

        } catch (Exception $ex) {
            Log::error(self::$GATEWAY_CODE . '-> saveAllProducts(): ' . $ex->getMessage());

            return back()->with(['message' => $ex->getMessage(), 'type' => 'error']);
        }
    }

    public static function saveProduct($plan)
    {
        $client = self::client();

        try {
            $request = $client->request('post', 'api/v2/billing/details', [
                'payment_method'         => FrequencyEnum::MONTHLY->value,
                'price_currency'         => $plan->currency,
                'receive_currency'       => $plan->currency,
                'title'                  => $plan->name,
                'description'            => $plan->name,
                'details_id'             => $plan->id,
                'callback_url'           => Helper::setting('site_url') . '/webhooks/coingate',
                'send_paid_notification' => true,
                'send_payment_email'     => false,
                'underpaid_cover_pct'    => '"0.0"',
                'items'                  => [
                    [
                        'description' => $plan->name . ' item',
                        'price'       => $plan->getAttribute('price'),
                        'currency'    => $plan->getAttribute('currency'),
                        'quantity'    => 1,
                        'item_id'     => $plan->id,
                    ],
                ],
            ]);

            $id = null;

            $request = self::objectToArray($request);

            if (is_array($request)) {
                if (array_key_exists('id', $request)) {
                    $id = $request['id'];
                }
            }

            if ($id) {
                $product = GatewayProducts::query()
                    ->firstOrCreate([
                        'plan_id'       => $plan->id,
                        'gateway_code'  => self::$GATEWAY_CODE,
                        'gateway_title' => self::$GATEWAY_NAME,
                    ]);

                $oldProductId = $product->product_id;

                $wasRecentlyCreated = $product->wasRecentlyCreated;

                $product->update([
                    'plan_name'  => $plan->name,
                    'product_id' => $id,
                    'payload'    => $request,
                ]);

                if (! $wasRecentlyCreated && $oldProductId) {
                    OldGatewayProducts::query()->firstOrCreate([
                        'plan_id'        => $plan->id,
                        'plan_name'      => $plan->name,
                        'gateway_code'   => self::$GATEWAY_CODE,
                        'product_id'     => $product->product_id,
                        'old_product_id' => $oldProductId,
                        'status'         => 'check',
                    ]);
                }
            }
        } catch (Exception $ex) {
            Log::error(self::$GATEWAY_CODE . '-> saveProduct(): ' . $ex->getMessage());

            return back()->with(['message' => $ex->getMessage(), 'type' => 'error']);
        }
    }

    public static function subscribe($plan): View
    {
        $product = GatewayProducts::where(['plan_id' => $plan->id, 'gateway_code' => self::$GATEWAY_CODE])->first();

        if ($product == null) {
            self::saveProduct($plan);
        }

        $order_id = 'ORDER-' . strtoupper(Str::random(13));

        $newDiscountedPrice = null;

        return view('panel.user.finance.subscription.' . self::$GATEWAY_CODE, compact('plan', 'order_id', 'newDiscountedPrice'));
    }

    public static function subscribeCheckout(Request $request, $referral = null)
    {

        $planID = $request->input('planID', null);
        $orderID = $request->input('orderID', null);
        $couponID = $request->input('couponID', null);

        $plan = Plan::query()->where('id', $planID)->first();

        $total = $plan->price;

        $user = self::createSubscriber();

        $gatewayProduct = GatewayProducts::query()
            ->where('plan_id', $plan->getAttribute('id'))
            ->where('gateway_code', self::$GATEWAY_CODE)
            ->first();

        if (! $gatewayProduct) {
            return back()->with(['message' => __('Gateway product not found'), 'type' => 'error']);
        }

        $client = self::client();

        $request = $client->request('POST', 'api/v2/billing/subscriptions', [
            'subscription_id' => 'TEST-' . $user->id,
            'subscriber'      => $user->getAttribute('coingate_subscriber_id'),
            'details'         => $gatewayProduct->getAttribute('product_id'),
            'start_date'      => date('Y-m-d H:i:s'),
        ]);

        $request = self::objectToArray($request);

        if (array_key_exists('id', $request)) {
            $id = $request['id'];

            // payment activate
            $client->request(
                'PATCH',
                'api/v2/billing/subscriptions/' . $id . '/activate', [
                    'id' => $id,
                ]);

            $payment = $client->request(
                'GET',
                '/api/v2/billing/subscriptions/' . $id . '/payments', [
                    'id' => $id,
                ]);

            $payment = self::objectToArray($payment);

            if (array_key_exists('id', $payment)) {

                $payments = data_get($payment, 'payments');

                if (is_array($payments) && count($payments) > 0) {
                    $last = Arr::last($payments);

                    $payment_url = data_get($last, 'payment_url');

                    if ($payment_url) {

                        try {
                            Subscriptions::query()
                                ->create([
                                    'user_id'       => $user->id,
                                    'name'          => $plan->id,
                                    'stripe_id'     => $id,
                                    'stripe_status' => 'WAITING',
                                    'stripe_price'  => $payment['id'],
                                    'quantity'      => 1,
                                    'trial_ends_at' => null,
                                    'tax_rate'      => 0,
                                    'tax_value'     => 0,
                                    'coupon'        => null,
                                    'total_amount'  => $total,
                                    'plan_id'       => $plan->id,
                                    'paid_with'     => self::$GATEWAY_CODE,

                                ]);

                            $order = UserOrder::query()
                                ->create([
                                    'order_id'           => $payment['id'],
                                    'plan_id'            => $plan->id,
                                    'user_id'            => $user->id,
                                    'payment_type'       => self::$GATEWAY_CODE,
                                    'price'              => $total,
                                    'affiliate_earnings' => 0,
                                    'status'             => 'WAITING',
                                    'country'            => $user->country ?? 'Unknown',
                                    'tax_rate'           => 0,
                                    'tax_value'          => 0,
                                    'payload'            => $payment,
                                ]);

                            // sent mail if required here later
                            CreateActivity::for($order->user, __('Purchased'), $order->plan->name . ' ' . __('Plan') . ' ' . __('For free'));
                            EmailPaymentConfirmation::create($user, $plan)->send();
                            \App\Models\Usage::getSingle()->updateSalesCount($total);

                            return redirect($payment_url);
                        } catch (Exception $th) {
                            Log::error(self::$GATEWAY_CODE . '-> subscribe(): ' . $th->getMessage());

                            return back()->with(['message' => Str::before($th->getMessage(), ':'), 'type' => 'error']);
                        }
                    }
                }
            }
        }

        return back()->with(['message' => __('Subscription failed'), 'type' => 'error']);
    }

    public static function handleWebhook(Request $request)
    {
        $orderId = $request->input('order_id', null);
        $token = $request->input('token', null);

        if ($orderId) {

            $order = UserOrder::query()
                ->where('type', 'token-pack')
                ->where('order_id', $orderId)
                ->where('payment_type', self::$GATEWAY_CODE)
                ->first();

            if ($order) {
                $status = $request->input('status', null);
                if ($status == 'paid') {

                    /**
                     * @var Plan $plan
                     */
                    $plan = Plan::query()->where('id', $order->plan_id)->first();

                    /**
                     * @var User $user
                     */
                    $user = User::query()->where('id', $order->user_id)->first();

                    self::creditIncreaseSubscribePlan($user, $plan);

                    $order->update(['status' => 'PAID']);

                    $subscription = Subscriptions::query()
                        ->where('paid_with', self::$GATEWAY_CODE)
                        ->where('stripe_id', $order->getAttribute('order_id'))
                        ->first();

                    if ($subscription) {
                        $subscription->stripe_status = 'active';
                        $subscription->save();
                    }
                }
            }
        }

        return true;
    }

    public static function prepaidCheckout(Request $request, $referral = null)
    {
        $planID = $request->input('planID', null);

        $orderID = $request->input('orderID', null);

        $plan = Plan::query()->where('id', $planID)->first();

        $user = Auth::user();

        $order = UserOrder::query()
            ->create([
                'order_id'           => $orderID,
                'plan_id'            => $plan->id,
                'user_id'            => $user->id,
                'payment_type'       => self::$GATEWAY_CODE,
                'price'              => $plan->price,
                'affiliate_earnings' => 0,
                'status'             => 'WAITING',
                'country'            => $user->country ?? 'Unknown',
                'tax_rate'           => 0,
                'tax_value'          => 0,
                'type'               => 'token-pack',
                'payload'            => [],
            ]);

        $client = self::client();

        $request = $client->request('POST', 'api/v2/orders', [
            'order_id'         => $orderID,
            'price_amount'     => $plan->price,
            'price_currency'   => $plan->currency,
            'receive_currency' => $plan->currency,
            'title'            => $plan->name,
            'description'      => $plan->name,
            'callback_url'     => Helper::setting('site_url') . '/webhooks/coingate',
            'cancel_url'       => Helper::setting('site_url') . '/dashboard',
            'success_url'      => Helper::setting('site_url') . '/dashboard/user/payment/succesful',
            'token'            => base64_encode($orderID),
            'purchaser_email'  => $user->email,
        ]);

        $request = self::objectToArray($request);

        $order->update(['payload' => $request]);

        if (array_key_exists('id', $request)) {
            $id = $request['id'];

            $order->update(['order_id' => $id]);
            \App\Models\Usage::getSingle()->updateSalesCount($plan->price);

            return redirect(data_get($request, 'payment_url'));
        }

        return back()->with(['message' => __('Subscription failed'), 'type' => 'error']);
    }

    public static function prepaid($plan)
    {
        $product = GatewayProducts::where(['plan_id' => $plan->id, 'gateway_code' => self::$GATEWAY_CODE])->first();
        if ($product == null) {
            self::saveProduct($plan);
        }

        $order_id = 'ORDER-' . strtoupper(Str::random(13));

        return view('panel.user.finance.prepaid.' . self::$GATEWAY_CODE, compact('plan', 'order_id'));
    }

    public static function subscribeCancel(?User $internalUser = null)
    {
        $user = Auth::user() ?: $internalUser;

        $userId = $user->getAttribute('id');

        // Get current active subscription
        $activeSub = getCurrentActiveSubscription($userId);

        if (! $activeSub) {
            return;
        }

        /**
         * @var Plan $plan
         */
        $plan = Plan::query()
            ->where('id', $activeSub->getAttribute('plan_id'))
            ->first();

        $id = $activeSub->getAttribute('stripe_price');

        $client = self::client()->request('PATCH', 'api/v2/billing/subscriptions/' . $id . '/cancel', []);

        $request = self::objectToArray($client);

        $status = data_get($request, 'status');

        if ($status == 'canceled') {
            $activeSub->stripe_status = $status;
            $activeSub->ends_at = \Carbon\Carbon::now();
            $activeSub->save();

            self::creditDecreaseCancelPlan($user, $plan);

            $user->save();

            CreateActivity::for($user, 'Cancelled', 'Subscription plan');

            return back()->with(['message' => __('Your subscription is cancelled succesfully.'), 'type' => 'success']);
        }

        return back()->with(['message' => __('Could not find active subscription. Nothing changed!'), 'type' => 'error']);
    }

    public static function cancelSubscribedPlan($subscription, $planId)
    {
        $user = Auth::user();

        $check = $subscription instanceof Subscriptions;

        if (! $check) {
            $subscription = Subscriptions::where('id', $subscription)->first();
        }

        try {
            $user = Auth::user();
            $id = $subscription->getAttribute('stripe_price');

            self::client()->request('PATCH', 'api/v2/billing/subscriptions/' . $id . '/cancel', []);

            $user->save();

            return true;
        } catch (Exception $th) {
            Log::error(self::$GATEWAY_CODE . "-> cancelSubscribedPlan():\n" . $th->getMessage());
            $plan = Plan::where('id', $planId)->first();

            $subscription->stripe_status = 'cancelled';
            $subscription->save();

            self::creditDecreaseCancelPlan($user, $plan);

            return true;
        }
    }

    public static function checkIfTrial(): bool
    {
        return false;
    }

    public static function getSubscriptionRenewDate()
    {
        $user = Auth::user() ?: User::query()->first();

        $subscription = getCurrentActiveSubscription($user->getAttribute('id'));

        $next_delivery_date = null;

        if ($subscription) {

            $id = $subscription->getAttribute('stripe_price');

            $path = 'api/v2/billing/subscriptions/' . $id . '';

            $client = self::client()
                ->request('get', $path, [
                    'id' => $id,
                ]);

            $request = self::objectToArray($client);

            $next_delivery_date = data_get($request, 'next_delivery_date');

        }

        if ($next_delivery_date) {
            return Carbon::create($next_delivery_date)->format('F jS, Y');
        }

        return false;
    }

    public static function getSubscriptionStatus($incomingUserId = null): bool
    {
        $user = null;

        if ($incomingUserId) {
            $user = User::query()->where('id', $incomingUserId)->first();
        }

        if (Auth::check() && $incomingUserId == null) {
            $user = Auth::user();
        }

        if (! $user) {
            return false;
        }

        $subscription = getCurrentActiveSubscription($user->getAttribute('id'));

        if ($subscription) {
            $id = $subscription->getAttribute('stripe_price');

            $path = 'api/v2/billing/subscriptions/' . $id;

            try {
                $client = self::client()
                    ->request('get', $path, [
                        'id' => $id,
                    ]);

                $request = self::objectToArray($client);

                $status = data_get($request, 'status');

                if ($status == 'active') {
                    return true;
                } else {
                    if ($subscription->getAttribute('created_at') < Carbon::now()->subHours(2)) {
                        $subscription->update([
                            'stripe_status' => 'cancelled',
                            'ends_at'       => \Carbon\Carbon::now(),
                        ]);
                    }

                    return false;
                }
            } catch (Exception $th) {
                if ($subscription->getAttribute('created_at') < Carbon::now()->subHours(2)) {
                    $subscription->update([
                        'stripe_status' => 'cancelled',
                        'ends_at'       => \Carbon\Carbon::now(),
                    ]);
                }

                return false;
            }
        }

        return false;
    }

    public static function getSubscriptionDaysLeft()
    {
        $user = null;

        if (Auth::check()) {
            $user = Auth::user();
        }

        if (! $user) {
            return;
        }

        $subscription = getCurrentActiveSubscription($user->getAttribute('id'));

        if ($subscription) {
            $id = $subscription->getAttribute('stripe_price');

            try {
                $path = 'api/v2/billing/subscriptions/' . $id;

                $client = self::client()
                    ->request('get', $path, [
                        'id' => $id,
                    ]);

                $request = self::objectToArray($client);

                $next_delivery_date = data_get($request, 'next_delivery_date');

                if ($next_delivery_date) {
                    $next_delivery_date = Carbon::create($next_delivery_date);

                    return $next_delivery_date->diffInDays(Carbon::now());
                }
            } catch (Exception $th) {
                return false;
            }
        }
    }

    public static function client()
    {
        $gateway = self::geteway();

        if (! $gateway) {
            return;
        }

        $mode = $gateway->getAttribute('mode') == 'sandbox';

        $secret = $mode == 'sandbox'
            ? $gateway->getAttribute('sandbox_client_secret')
            : $gateway->getAttribute('live_client_secret');

        $client = new \CoinGate\Client($secret, $mode);

        return $client;
    }

    public static function geteway(): Model|Builder|null
    {
        if (self::$gateway) {
            return self::$gateway;
        }

        self::$gateway = Gateways::where('code', 'coingate')->first();

        return self::$gateway;
    }

    public static function createSubscriber()
    {
        $client = self::client();

        $user = Auth::user() ?: User::query()->first();

        if (is_null($user->coingate_subscriber_id)) {
            $request = $client->request('post', '/api/v2/billing/subscribers', [
                'email'         => $user->email,
                'subscriber_id' => Auth::id(),
                'first_name'    => $user->name,
                'last_name'     => $user->surname,
            ]);

            $request = self::objectToArray($request);

            if (array_key_exists('id', $request)) {
                $id = $request['id'];

                if ($id) {
                    $user->update([
                        'coingate_subscriber_id' => $id,
                    ]);
                }
            }
        }

        return $user;
    }

    public static function checkPayments()
    {

        $date = now()->subHours(2);

        $orders = UserOrder::query()
            ->where('payment_type', self::$GATEWAY_CODE)
            ->where('status', 'WAITING')
            ->where('created_at', '>', $date)
            ->get();

        foreach ($orders as $order) {
            $payments = $order->payload['payments'];

            $last = Arr::last($payments);

            $path = 'api/v2/billing/payments/' . $last['id'];

            try {
                $client = self::client()
                    ->request('get', $path, [
                        'id' => $last['id'],
                    ]);

                $request = self::objectToArray($client);

                $status = data_get($request, 'status');

                if ($status == 'paid') {

                    /**
                     * @var User $user
                     */
                    $user = User::query()->where('id', $order->getAttribute('user_id'))->first();

                    /**
                     * @var Plan $plan
                     */
                    $plan = Plan::query()->where('id', $order->getAttribute('plan_id'))->first();

                    $order->update([
                        'status' => 'PAID',
                    ]);

                    self::creditIncreaseSubscribePlan($user, $plan);

                    $subscription = Subscriptions::query()
                        ->where('stripe_id', $order->getAttribute('order_id'))
                        ->first();

                    if ($subscription) {
                        $subscription->stripe_status = 'active';
                        $subscription->save();
                    }

                    CreateActivity::for($user, 'Purchased', $plan->name . ' Plan');
                    EmailPaymentConfirmation::create($user, $plan)->send();
                }
            } catch (Exception $th) {

            }
        }
    }

    public static function objectToArray($request)
    {
        if (is_object($request)) {
            if (property_exists($request, 'id')) {
                $request = json_decode(json_encode($request), true);
            }
        }

        return $request;
    }

    public static function gatewayDefinitionArray(): array
    {
        return [
            'code'                  => 'coingate',
            'title'                 => 'Coingate',
            'link'                  => 'https://coingate.com/',
            'active'                => 0,                      // if user activated this gateway - dynamically filled in main page
            'available'             => 1,                   // if gateway is available to use
            'img'                   => '/assets/img/payments/coingate.svg',
            'whiteLogo'             => 0,                   // if gateway logo is white
            'mode'                  => 1,                        // Option in settings - Automatically set according to the "Development" mode. "Development" ? sandbox : live (PAYPAL - 1)
            'sandbox_client_id'     => 0,           // Option in settings 0-Hidden 1-Visible
            'sandbox_client_secret' => 1,       // Option in settings
            'sandbox_app_id'        => 0,              // Option in settings
            'live_client_id'        => 0,              // Option in settings
            'live_client_secret'    => 1,          // Option in settings
            'live_app_id'           => 0,                 // Option in settings
            'currency'              => 0,                    // Option in settings
            'currency_locale'       => 0,             // Option in settings
            'base_url'              => 0,                    // Option in settings
            'sandbox_url'           => 0,                 // Option in settings
            'locale'                => 0,                      // Option in settings
            'validate_ssl'          => 0,                // Option in settings
            'logger'                => 0,                      // Option in settings
            'notify_url'            => 0,                  // Gateway notification url at our side
            'webhook_secret'        => 0,              // Option in settings
            'tax'                   => 1,              // Option in settings
            'bank_account_details'  => 0,
            'bank_account_other'    => 0,
        ];
    }
}
