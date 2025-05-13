<?php

namespace App\Services\PaymentGateways;

use App\Actions\CreateActivity;
use App\Enums\Plan\FrequencyEnum;
use App\Models\GatewayProducts;
use App\Models\Gateways;
use App\Models\Plan;
use App\Models\Usage;
use App\Models\User;
use App\Models\UserOrder;
use App\Services\Contracts\BaseGatewayService;
use App\Services\PaymentGateways\Contracts\CreditUpdater;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Cashier\Subscription as Subscriptions;
use Paddle\SDK\Client;
use Paddle\SDK\Entities\Shared\CountryCode;
use Paddle\SDK\Entities\Shared\CurrencyCode;
use Paddle\SDK\Entities\Shared\CustomData;
use Paddle\SDK\Entities\Shared\Interval;
use Paddle\SDK\Entities\Shared\Money;
use Paddle\SDK\Entities\Shared\PriceQuantity;
use Paddle\SDK\Entities\Shared\TaxCategory;
use Paddle\SDK\Entities\Shared\TimePeriod;
use Paddle\SDK\Entities\Subscription\SubscriptionEffectiveFrom;
use Paddle\SDK\Environment;
use Paddle\SDK\Exceptions\ApiError;
use Paddle\SDK\Exceptions\ApiError\PriceApiError;
use Paddle\SDK\Exceptions\ApiError\ProductApiError;
use Paddle\SDK\Exceptions\SdkExceptions\MalformedResponse;
use Paddle\SDK\Options;
use Paddle\SDK\Resources\Addresses\Operations\CreateAddress;
use Paddle\SDK\Resources\Customers\Operations\CreateCustomer;
use Paddle\SDK\Resources\Customers\Operations\ListCustomers;
use Paddle\SDK\Resources\Prices\Operations\CreatePrice;
use Paddle\SDK\Resources\Products\Operations\CreateProduct;
use Paddle\SDK\Resources\Subscriptions\Operations\CancelSubscription;
use Paddle\SDK\Resources\Subscriptions\Operations\ListSubscriptions;
use Paddle\SDK\Undefined;

class PaddleService implements BaseGatewayService
{
    use CreditUpdater;

    public static $gateway;

    protected static string $GATEWAY_CODE = 'paddle';

    protected static string $GATEWAY_NAME = 'Paddle';

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

        $paddle = self::client();

        $stripe_product_id = null;

        try {
            $product = $paddle->products->create(new CreateProduct(
                name: $plan['name'],
                taxCategory: TaxCategory::Standard(),
                description: $plan['name'],
                imageUrl: null,
                customData: new CustomData(['plan_id' => $plan['id']]),
            ));

            $product = self::objectToArrayHaveId($product);

            $stripe_product_id = data_get($product, 'id');

        } catch (ProductApiError|ApiError|MalformedResponse $e) {

            return back()->with(['message' => $e->getMessage(), 'type' => 'error']);
        }

        try {
            $trialPeriod = new Undefined;

            if ($plan->trial_days and $plan->type != 'prepaid') {
                $trialPeriod = new TimePeriod(Interval::Day(), $plan->trial_days);
            }

            $billingCycle = null;

            if ($plan->type != 'prepaid') {
                $billingCycle = $plan->frequency == FrequencyEnum::MONTHLY->value ? Interval::Month() : Interval::Year();

                $billingCycle = new TimePeriod($billingCycle, 1);
            }

            $price = $paddle->prices->create(new CreatePrice(
                description: 'Bear Hug',
                productId: $stripe_product_id,
                unitPrice: new Money($plan['price'] * 100, CurrencyCode::USD()),
                unitPriceOverrides: [],
                trialPeriod: $trialPeriod,
                billingCycle: $billingCycle,
                quantity: new PriceQuantity(1, 1),
                customData: new CustomData([
                    'product' => json_encode($product),
                ]),
            ));

            $price = self::objectToArrayHaveId($price);

            $gateProduct = GatewayProducts::query()
                ->firstOrCreate([
                    'plan_id'       => $plan->id,
                    'gateway_code'  => self::$GATEWAY_CODE,
                    'gateway_title' => self::$GATEWAY_NAME,
                ]);

            $gateProduct->update([
                'product_id' => $product['id'],
                'price_id'   => $price['id'],
                'payload'    => json_encode($price),
            ]);

        } catch (PriceApiError|ApiError|MalformedResponse $e) {
            return back()->with(['message' => $e->getMessage(), 'type' => 'error']);
        }
    }

    public static function subscribe($plan)
    {
        $gateProduct = GatewayProducts::query()
            ->where('gateway_code', self::$GATEWAY_CODE)
            ->where('plan_id', $plan->getAttribute('id'))
            ->orderBy('id', 'desc')
            ->whereNotNull('price_id')
            ->value('price_id');

        if ($gateProduct == null) {
            return back()->with(['message' => __('Please enable paddle'), 'type' => 'error']);
        }

        $user = auth()->user() ?: User::query()->first();

        $gateway = self::geteway();

        if ($gateway == null) {
            return back()->with(['message' => __('Please enable paddle'), 'type' => 'error']);
        }

        $token = $gateway->getAttribute('live_client_id');

        if ($gateway->getAttribute('mode') == 'sandbox') {
            $token = $gateway->getAttribute('sandbox_client_id');
        }

        $paddle = self::client();

        $orderId = Str::random(10);

        UserOrder::query()
            ->create([
                'order_id'           => $orderId,
                'plan_id'            => $plan->id,
                'user_id'            => $user->id,
                'payment_type'       => self::$GATEWAY_CODE,
                'price'              => $plan->price,
                'affiliate_earnings' => 0,
                'status'             => 'WAITING',
                'country'            => $user->country ?? 'Unknown',
                'tax_rate'           => 0,
                'tax_value'          => 0,
                'type'               => $plan->type == 'prepaid' ? 'token-pack' : 'subscription',
                'payload'            => [],
            ]);

        try {
            if (($customerId = $user->getAttribute('stripe_id')) && ($addressId = $user->getAttribute('address_id'))) {
                return view('panel.user.finance.subscription.' . self::$GATEWAY_CODE, compact('plan', 'customerId', 'orderId', 'token', 'gateProduct', 'gateway'));
            }

            $findCustomer = $paddle
                ->customers
                ->list(new ListCustomers(
                    emails: [$user->getAttribute('email')]
                ));

            if ($findCustomer && $findCustomer->valid()) {
                $customer = $findCustomer->current();
            } else {
                $customer = $paddle
                    ->customers
                    ->create(new CreateCustomer(
                        email: $user->getAttribute('email') ?: 'test@test.com',
                        name: $user->getAttribute('name') ?: 'Test User',
                        customData: new CustomData(['user_id' => $user->getAttribute('id')]),
                        locale: 'en',
                    ));

            }

            $customer = self::objectToArrayHaveId($customer);

            $customerId = data_get($customer, 'id');

            $user->update([
                'stripe_id' => $customerId,
            ]);

            $address = $paddle->addresses->create($customerId, new CreateAddress(
                countryCode: CountryCode::US(),
                description: 'Home',
                firstLine: 'no',
                secondLine: '',
                city: '',
                postalCode: '23232',
                region: 'US',
                customData: new CustomData(['user_id' => $user->getAttribute('id'), 'customer_id' => $customerId]),
            ));

            $address = self::objectToArrayHaveId($address);

            $customer['address'] = $address;

            $user->update([
                'address_id'       => $address['id'],
                'customer_payload' => $customer,
            ]);

            $orderId = Str::random(10);

            return view('panel.user.finance.subscription.' . self::$GATEWAY_CODE, compact('plan', 'customerId', 'orderId', 'token', 'gateProduct', 'gateway'));

        } catch (Exception $ex) {
            Log::error(self::$GATEWAY_CODE . '-> subscribe(): ' . $ex->getMessage());

            return back()->with(['message' => $ex->getMessage(), 'type' => 'error']);
        }
    }

    public static function subscribeCheckout(Request $request, $referral = null)
    {
        try {
            $checkoutData = $request->all();

            if ($checkoutData == null) {
                return back()->with(['message' => __('Please error'), 'type' => 'error']);
            }

            $planId = $checkoutData['planID'];

            $plan = Plan::query()->where('id', $planId)->first();

            $data = json_decode($checkoutData['checkoutData'], true);

            $customerId = data_get($data, 'data.customer.id');

            $paddle = self::client();

            $subscriptions = $paddle->subscriptions->list(new ListSubscriptions(
                customerIds: [$customerId]
            ));

            $current = $subscriptions->current();

            $currentArray = json_decode(json_encode($current), true);

            $price_id_product = data_get($data, 'data.items.0.price_id');

            $subscription = new Subscriptions;
            $subscription->user_id = auth()->id();
            $subscription->name = $planId;
            $subscription->stripe_id = $currentArray['id'];
            $subscription->stripe_status = $plan->trial_days != 0 ? 'trialing' : 'active';
            $subscription->stripe_price = $price_id_product;
            $subscription->quantity = 1;
            $subscription->trial_ends_at = null;
            $subscription->ends_at = $plan->frequency == FrequencyEnum::LIFETIME_MONTHLY->value ? Carbon::now()->addMonths(1) : Carbon::now()->addYears(1);
            $subscription->auto_renewal = 1;
            $subscription->plan_id = $plan->id;
            $subscription->paid_with = self::$GATEWAY_CODE;
            $subscription->save();
            $orderId = $request->input('orderID', null);

            if ($orderId) {

                $order = UserOrder::query()
                    ->where('order_id', $orderId)
                    ->where('payment_type', self::$GATEWAY_CODE)
                    ->first();

                if ($order) {
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

                    Usage::getSingle()->updateSalesCount($plan->price);
                }
            }

            return to_route('dashboard.user.payment.subscription')
                ->with([
                    'message' => __('Subscription created successfully'), 'type' => 'success',
                ]);
        } catch (Exception $ex) {
            Log::error(self::$GATEWAY_CODE . '-> subscribeCheckout(): ' . $ex->getMessage());

            return to_route('dashboard.user.payment.subscription')->with(['message' => $ex->getMessage(), 'type' => 'error']);
        }

        return true;
        // TODO: Implement subscribeCheckout() method.
    }

    public static function prepaidCheckout(Request $request, $referral = null)
    {
        try {
            $checkoutData = $request->all();

            if ($checkoutData == null) {
                return back()->with(['message' => __('Please error'), 'type' => 'error']);
            }

            $planId = $checkoutData['planID'];

            $plan = Plan::query()->where('id', $planId)->first();

            $data = json_decode($checkoutData['checkoutData'], true);

            $customerId = data_get($data, 'data.customer.id');

            $paddle = self::client();

            $subscriptions = $paddle->subscriptions->list(new ListSubscriptions(
                customerIds: [$customerId]
            ));

            $current = $subscriptions->current();

            //            $currentArray = json_decode(json_encode($current), true);

            //            $price_id_product = data_get($data, 'data.items.0.price_id');
            //
            //            $subscription = new Subscriptions();
            //            $subscription->user_id = auth()->id();
            //            $subscription->name = $planId;
            //            $subscription->stripe_id = $currentArray['id'];
            //            $subscription->stripe_status = $plan->trial_days != 0 ? "trialing" : "active";
            //            $subscription->stripe_price = $price_id_product;
            //            $subscription->quantity = 1;
            //            $subscription->trial_ends_at = null;
            //            $subscription->ends_at = $plan->frequency == 'lifetime_monthly' ? Carbon::now()->addMonths(1) : Carbon::now()->addYears(1);
            //            $subscription->auto_renewal = 1;
            //            $subscription->plan_id = $plan->id;
            //            $subscription->paid_with = self::$GATEWAY_CODE;
            //            $subscription->save();
            $orderId = $request->input('orderID', null);

            if ($orderId) {

                $order = UserOrder::query()
                    ->where('order_id', $orderId)
                    ->where('payment_type', self::$GATEWAY_CODE)
                    ->first();

                if ($order) {
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

                    Usage::getSingle()->updateSalesCount($plan->price);
                }
            }

            return to_route('dashboard.user.payment.subscription')
                ->with([
                    'message' => __('Payment successfully'), 'type' => 'success',
                ]);
        } catch (Exception $ex) {
            Log::error(self::$GATEWAY_CODE . '-> subscribeCheckout(): ' . $ex->getMessage());

            return to_route('dashboard.user.payment.subscription')->with(['message' => $ex->getMessage(), 'type' => 'error']);
        }
    }

    public static function prepaid($plan)
    {
        $gateProduct = GatewayProducts::query()
            ->where('gateway_code', self::$GATEWAY_CODE)
            ->where('plan_id', $plan->getAttribute('id'))
            ->orderBy('id', 'desc')
            ->whereNotNull('price_id')
            ->value('price_id');

        if ($gateProduct == null) {
            return back()->with(['message' => __('Please enable paddle'), 'type' => 'error']);
        }

        $user = auth()->user() ?: User::query()->first();

        $gateway = self::geteway();

        if ($gateway == null) {
            return back()->with(['message' => __('Please enable paddle'), 'type' => 'error']);
        }

        $token = $gateway->getAttribute('live_client_id');

        if ($gateway->getAttribute('mode') == 'sandbox') {
            $token = $gateway->getAttribute('sandbox_client_id');
        }

        $paddle = self::client();

        $orderId = Str::random(10);

        UserOrder::query()
            ->create([
                'order_id'           => $orderId,
                'plan_id'            => $plan->id,
                'user_id'            => $user->id,
                'payment_type'       => self::$GATEWAY_CODE,
                'price'              => $plan->price,
                'affiliate_earnings' => 0,
                'status'             => 'WAITING',
                'country'            => $user->country ?? 'Unknown',
                'tax_rate'           => 0,
                'tax_value'          => 0,
                'type'               => $plan->type == 'prepaid' ? 'token-pack' : 'subscription',
                'payload'            => [],
            ]);

        try {
            if (($customerId = $user->getAttribute('stripe_id')) && ($addressId = $user->getAttribute('address_id'))) {
                return view('panel.user.finance.prepaid.' . self::$GATEWAY_CODE, compact('plan', 'customerId', 'orderId', 'token', 'gateProduct'));
            }

            $findCustomer = $paddle
                ->customers
                ->list(new ListCustomers(
                    emails: [$user->getAttribute('email')]
                ));

            if ($findCustomer && $findCustomer->current()) {
                $customer = $findCustomer->current();
            } else {
                $customer = $paddle
                    ->customers
                    ->create(new CreateCustomer(
                        email: $user->getAttribute('email') ?: 'test@test.com',
                        name: $user->getAttribute('name') ?: 'Test User',
                        customData: new CustomData(['user_id' => $user->getAttribute('id')]),
                        locale: 'en',
                    ));
            }

            $customer = self::objectToArrayHaveId($customer);

            $customerId = data_get($customer, 'id');

            $user->update([
                'stripe_id' => $customerId,
            ]);

            $address = $paddle->addresses->create($customerId, new CreateAddress(
                countryCode: CountryCode::US(),
                description: 'Home',
                firstLine: 'no',
                secondLine: '',
                city: '',
                postalCode: '23232',
                region: 'US',
                customData: new CustomData(['user_id' => $user->getAttribute('id'), 'customer_id' => $customerId]),
            ));

            $address = self::objectToArrayHaveId($address);

            $customer['address'] = $address;

            $user->update([
                'address_id'       => $address['id'],
                'customer_payload' => $customer,
            ]);

            $orderId = Str::random(10);

            return view('panel.user.finance.prepaid.' . self::$GATEWAY_CODE, compact('plan', 'customerId', 'orderId', 'token', 'gateProduct', 'gateway'));

        } catch (Exception $ex) {
            Log::error(self::$GATEWAY_CODE . '-> subscribe(): ' . $ex->getMessage());

            return back()->with(['message' => $ex->getMessage(), 'type' => 'error']);
        }
    }

    public static function subscribeCancel(?User $internalUser = null)
    {
        $user = Auth::user() ?: $internalUser;

        $userId = $user->getAttribute('id');

        // Get current active subscription
        $activeSub = getCurrentActiveSubscription($userId);

        if (! $activeSub) {
            return back()->with(['message' => __('Error!'), 'type' => 'error']);
        }

        /**
         * @var Plan $plan
         */
        $plan = Plan::query()
            ->where('id', $activeSub->getAttribute('plan_id'))
            ->first();

        $paddle = self::client();

        $data = $paddle->subscriptions->cancel(
            $activeSub->getAttribute('stripe_id'),
            new CancelSubscription(SubscriptionEffectiveFrom::Immediately())
        );

        $status = data_get($data, 'data.status');

        if ($status == 'canceled') {
            $activeSub->stripe_status = $status;
            $activeSub->ends_at = Carbon::now();
            $activeSub->save();

            self::creditDecreaseCancelPlan($user, $plan);

            CreateActivity::for($user, 'Cancelled', 'Subscription plan');

            return back()->with(['message' => __('Your subscription is cancelled successfully.'), 'type' => 'success']);
        }

        return back()->with(['message' => __('Could not find active subscription. Nothing changed!'), 'type' => 'error']);
    }

    public static function cancelSubscribedPlan($subscription, $planId)
    {

        $paddle = self::client();

        $user = Auth::user();

        $check = $subscription instanceof Subscriptions;

        if (! $check) {
            $subscription = Subscriptions::where('id', $subscription)->first();
        }

        if ($subscription == null) {
            return back()->with(['message' => __('Subscription not found'), 'type' => 'error']);
        }

        try {

            $paddle->subscriptions->cancel(
                $subscription->getAttribute('stripe_id'),
                new CancelSubscription(SubscriptionEffectiveFrom::Immediately())
            );

            $subscription->update(['stripe_status' => 'canceled']);

        } catch (Exception $ex) {

            Log::error(self::$GATEWAY_CODE . '-> cancelSubscribedPlan(): ' . $ex->getMessage());

            return back()->with(['message' => $ex->getMessage(), 'type' => 'error']);
        }
    }

    public static function checkIfTrial()
    {
        // TODO: Implement checkIfTrial() method.
    }

    public static function getSubscriptionRenewDate()
    {
        $user = Auth::user() ?: User::query()->first();

        $subscription = getCurrentActiveSubscription($user->getAttribute('id'));

        $next_delivery_date = null;

        if ($subscription) {

            $id = $subscription->getAttribute('stripe_id');

            $request = self::client()->subscriptions->get($id);

            $next_delivery_date = data_get($request, 'nextBilledAt');
        }

        if ($next_delivery_date) {
            return Carbon::create($next_delivery_date)->format('F jS, Y');
        }

        return false;
    }

    public static function getSubscriptionStatus($incomingUserId = null)
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
            $id = $subscription->getAttribute('stripe_id');

            try {
                $request = self::client()->subscriptions->get($id);

                $status = data_get($request, 'status');

                if ((string) $status == 'active') {
                    return true;
                } else {
                    if ($subscription->getAttribute('created_at') < Carbon::now()->subHours(2)) {
                        $subscription->update([
                            'stripe_status' => 'cancelled',
                            'ends_at'       => Carbon::now(),
                        ]);
                    }

                    return false;
                }
            } catch (Exception $th) {
                if ($subscription->getAttribute('created_at') < Carbon::now()->subHours(2)) {
                    $subscription->update([
                        'stripe_status' => 'cancelled',
                        'ends_at'       => Carbon::now(),
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
            $id = $subscription->getAttribute('stripe_id');

            try {
                $request = self::client()->subscriptions->get($id);

                $next_delivery_date = data_get($request, 'nextBilledAt');

                if ($next_delivery_date) {
                    $next_delivery_date = Carbon::create($next_delivery_date);

                    return $next_delivery_date->diffInDays(Carbon::now());
                }
            } catch (Exception $th) {
                return false;
            }
        }
    }

    public static function handleWebhook(Request $request) {}

    public static function client()
    {
        $gateway = self::geteway();

        if (! $gateway) {
            return;
        }

        $apiKey = $gateway->getAttribute('live_client_secret');

        $environment = Environment::PRODUCTION;

        if ($gateway->getAttribute('mode') == 'sandbox') {
            $apiKey = $gateway->getAttribute('sandbox_client_secret');

            $environment = Environment::SANDBOX;
        }

        return new Client($apiKey, options: new Options($environment));
    }

    public static function geteway(): Model|Builder|null
    {
        if (self::$gateway) {
            return self::$gateway;
        }

        self::$gateway = Gateways::where('code', self::$GATEWAY_CODE)->first();

        return self::$gateway;
    }

    public static function objectToArrayHaveId($request)
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
            'code'                  => 'paddle',
            'title'                 => 'Paddle',
            'link'                  => 'https://paddle.com/',
            'active'                => 0,                      // if user activated this gateway - dynamically filled in main page
            'available'             => 1,                   // if gateway is available to use
            'img'                   => custom_theme_url('/assets/img/payments/paddle.svg'),
            'whiteLogo'             => 0,                   // if gateway logo is white
            'mode'                  => 1,                        // Option in settings - Automatically set according to the "Development" mode. "Development" ? sandbox : live (PAYPAL - 1)
            'sandbox_client_id'     => 1,           // Option in settings 0-Hidden 1-Visible
            'sandbox_client_secret' => 1,       // Option in settings
            'sandbox_app_id'        => 0,              // Option in settings
            'live_client_id'        => 1,              // Option in settings
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
