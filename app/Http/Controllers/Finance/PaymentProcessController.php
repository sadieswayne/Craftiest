<?php

namespace App\Http\Controllers\Finance;

use App\Actions\CreateActivity;
use App\Enums\Plan\FrequencyEnum;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\CustomBilingPlans;
use App\Models\Finance\Subscription;
use App\Models\GatewayProducts;
use App\Models\Gateways;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserOrder;
use App\Services\Common\MenuService;
use App\Services\GatewaySelector;
use App\Services\PaymentGateways\Contracts\CreditUpdater;
use App\Services\PaymentGateways\StripeService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Laravel\Cashier\Subscription as Subscriptions;

class PaymentProcessController extends Controller
{
    use CreditUpdater;

    public function prepaidStripeSuccess(Request $request, Plan $plan, User $user)
    {
        StripeService::getKey();

        $gateway = StripeService::getGateway();

        $checkoutSession = $request->user()->stripe()->checkout->sessions->retrieve($request->session_id);

        if ($checkoutSession?->payment_status === 'paid') {
            $order = new UserOrder;
            $order->order_id = 'SPO-' . strtoupper(Str::random(13));
            $order->plan_id = $plan->id;
            $order->user_id = $user->id;
            $order->type = 'prepaid';
            $order->payment_type = 'stirpe';
            $order->price = $plan->price;
            $order->affiliate_earnings = ($plan->price * Helper::setting('affiliate_commission_percentage')) / 100;
            $order->status = 'Success';
            $order->country = $user->country ?? 'Unknown';
            $order->tax_rate = $gateway->tax;
            $order->tax_value = taxToVal($plan->price, 0);
            $order->save();

            self::creditIncreaseSubscribePlan($user, $plan);
        }

        return redirect()->route('dashboard.user.payment.succesful')->with([
            'message' => __('Thank you for your purchase. Enjoy your remaining words and images.'),
            'type'    => 'success',
        ]);
    }

    public function stripeSuccess(Request $request, Subscription $subscription)
    {
        if ($subscription?->plan?->type === 'subscription') {
            StripeService::getKey();

            $checkoutSession = $subscription->user->stripe()->checkout->sessions->retrieve($subscription->stripe_id);

            $subscription->update(['stripe_id' => $checkoutSession->subscription]);

            return StripeService::subscribeCheckout(request: $request, subscription: $subscription);
        }
    }

    public function stripeCancel(Request $request, Subscription $subscription): RedirectResponse
    {
        return redirect()->route('dashboard.user.payment.subscription')->with(['message' => __('A problem occurred!'), 'type' => 'error']);
    }

    // payment area
    public function isActiveSubscription($planId): bool // Checks subscription table if given plan is active on user (already subscribed)
    {
        $activeSub = getCurrentActiveSubscription();
        if ($activeSub) {
            $activesubid = $activeSub->id;
        } else {
            $activeSub_yokassa = getCurrentActiveSubscriptionYokkasa();
            if ($activeSub_yokassa) {
                $activesubid = $activeSub_yokassa->id;
            } else {
                $activesubid = 0; // id can't be zero, so this will be easy to check instead of null
            }
        }

        return $activesubid === $planId;
    }

    public function startSubscriptionProcess($planId, $gatewayCode)
    { // when click on subscribe
        $plan = Plan::where('id', $planId)->first();
        if ($plan) {
            if ($this->isActiveSubscription($planId)) {
                return back()->with([
                    'message' => __('You already have subscription. Please cancel it before creating a new subscription'),
                    'type'    => 'error',
                ]);
            }
            if ($gatewayCode === 'walletmaxpay') {
                return back()->with([
                    'message' => __('WalletMaxPay available only for Token Packs'), 'type' => 'error',
                ]);
            }

            try {
                return GatewaySelector::selectGateway($gatewayCode)::subscribe($plan);
            } catch (Exception $e) {
                return back()->with(['message' => $e->getMessage(), 'type' => 'error']);
            }
        }
        abort(404);
    }

    public function startPrepaidPaymentProcess($planId, $gatewayCode)
    {
        $plan = Plan::where('id', $planId)->first();
        if ($plan) {
            try {
                return GatewaySelector::selectGateway($gatewayCode)::prepaid($plan);
            } catch (Exception $e) {
                return back()->with(['message' => $e->getMessage(), 'type' => 'error']);
            }
        }
        abort(404);
    }

    public function startSubscriptionCheckoutProcess(Request $request, $gateway = null, $referral = null): null|RedirectResponse|array|View
    {
        if ($gateway !== 'freeservice' && $request->isMethod('post')) {
            $gateways = Gateways::where('is_active', 1)->pluck('code')->toArray();
            $request->validate([
                'planID'   => 'required',
                'orderID'  => 'nullable',
                'couponID' => 'nullable',
                'gateway'  => ['required', 'in:' . implode(',', $gateways)],
            ]);
            if (is_null($gateway)) {
                $gateway = $request->gateway;
            }
        }

        try {
            return GatewaySelector::selectGateway($gateway)::subscribeCheckout($request, $referral);
        } catch (Exception $e) {
            return back()->with(['message' => $e->getMessage(), 'type' => 'error']);
        }
    }

    public function startPrepaidCheckoutProcess(Request $request, $gateway = null, $referral = null): null|RedirectResponse|View
    {
        if ($gateway !== 'freeservice' && $request->isMethod('post')) {
            $gateways = Gateways::where('is_active', 1)->pluck('code')->toArray();
            $request->validate([
                'planID'   => 'required',
                'orderID'  => 'nullable',
                'couponID' => 'nullable',
                'gateway'  => ['required', 'in:' . implode(',', $gateways)],
            ]);
            if (is_null($gateway)) {
                $gateway = $request->gateway;
            }
        }

        try {
            return GatewaySelector::selectGateway($gateway)::prepaidCheckout($request, $referral);
        } catch (Exception $e) {
            return back()->with(['message' => $e->getMessage(), 'type' => 'error']);
        }
    }

    // additional required functions
    public function createPayPalOrder(Request $request): null|RedirectResponse|View
    {
        try {
            return GatewaySelector::selectGateway('paypal')::createPayPalOrder($request);
        } catch (Exception $e) {
            return back()->with(['message' => $e->getMessage(), 'type' => 'error']);
        }
    }

    public function iyzicoPrepaidCallback(Request $request): null|RedirectResponse|View
    {
        try {
            return GatewaySelector::selectGateway('iyzico')::iyzicoPrepaidCallback($request);
        } catch (Exception $e) {
            return back()->with(['message' => $e->getMessage(), 'type' => 'error']);
        }
    }

    public function iyzicoSubscribeCallback(Request $request): null|RedirectResponse|View
    {
        try {
            return GatewaySelector::selectGateway('iyzico')::iyzicoSubscribeCallback($request);
        } catch (Exception $e) {
            return back()->with(['message' => $e->getMessage(), 'type' => 'error']);
        }
    }

    public function iyzicoProductsList(Request $request): null|RedirectResponse|View
    {
        try {
            return GatewaySelector::selectGateway('iyzico')::iyzicoProductsList($request);
        } catch (Exception $e) {
            return back()->with(['message' => $e->getMessage(), 'type' => 'error']);
        }
    }

    public function successful(): View
    {
        app(MenuService::class)->regenerate();

        return view('panel.user.finance.succesful');
    }

    // webhook control area
    public function handleWebhook(Request $request, $gateway)
    {
        try {
            if ($request->isMethod('post')) { // accept the post method for all
                if ($gateway === 'simulate') {
                    return GatewaySelector::selectGateway($gateway)::simulateWebhookEvent($request);
                }

                return GatewaySelector::selectGateway($gateway)::handleWebhook($request);
            }

            if ($request->isMethod('get')) { // accept the get method only for [twocheckout, simulate]
                if ($gateway === 'simulate') {
                    return GatewaySelector::selectGateway('paypal')::simulateWebhookEvent($request);
                }

                if ($gateway === 'twocheckout') {
                    return GatewaySelector::selectGateway($gateway)::handleWebhook($request);
                }

                if ($gateway === 'razorpay') {
                    return GatewaySelector::selectGateway($gateway)::handleWebhook($request);
                }

                abort(404);
            }
        } catch (Exception $e) {
            return back()->with(['message' => $e->getMessage(), 'type' => 'error']);
        }
    }

    // admin control area
    public static function bankTransactions(): View
    {
        $bankOrders = UserOrder::where('payment_type', 'banktransfer')->orderByRaw("CASE WHEN status = 'Waiting' THEN 0 ELSE 1 END")->orderBy('created_at', 'desc')->get();

        return view('panel.admin.banktransfer.index', compact('bankOrders'));
    }

    public static function bankDelete($id = null): null|RedirectResponse|View
    {
        $post = UserOrder::findOrFail($id);
        $post->delete();

        return back()->with(['message' => __('Deleted Successfully'), 'type' => 'success']);
    }

    public static function bankUpdateSave(Request $request): null|RedirectResponse|View
    {
        if ($request->order_status !== 0) {
            $order = UserOrder::findOrFail($request->order_id);
            self::changeOrderStatus($request->order_status, $order);
        }

        return back()->with(['message' => __('Updated Successfully'), 'type' => 'success']);
    }

    private static function changeOrderStatus($status, $order): void
    {
        switch ($status) {
            case 'Waiting':
                // sent mail if required here later
                CreateActivity::for($order->user, __('Bank transaction status updated to:') . ' ' . __($status), $order->plan->name . ' ' . __('Plan'));

                break;
            case 'Approved':
                if ($order->type === 'subscription') {
                    $subs = Subscriptions::where('stripe_id', $order->order_id)->first();
                    if ($subs) {
                        $subs->stripe_status = 'bank_approved';

                        switch ($order->plan->frequency) {
                            case FrequencyEnum::YEARLY->value:
                                $subs->ends_at = Carbon::now()->addYears(1);

                                break;
                            case FrequencyEnum::LIFETIME_MONTHLY->value:
                                $subs->ends_at = Carbon::now()->addMonths(1); // ends each month but auto renewing without payment reqs
                                $subs->auto_renewal = 1;

                                break;
                            case FrequencyEnum::LIFETIME_YEARLY->value:
                                $subs->ends_at = Carbon::now()->addYears(1); // ends each year but auto renewing without payment reqs
                                $subs->auto_renewal = 1;

                                break;
                            default:
                                // FrequencyEnum::MONTHLY->value
                                $subs->ends_at = Carbon::now()->addMonths(1);

                                break;
                        }
                        $subs->save();
                    }
                }
                self::creditIncreaseSubscribePlan($order->user, $order->plan);
                CreateActivity::for($order->user, __('Purchased with approved bank transaction'), $order->plan->name . ' ' . __('Plan'));

                break;
            case 'Rejected':
                $subs = Subscriptions::where('stripe_id', $order->order_id)->first();
                if ($subs) {
                    $subs->stripe_status = 'bank_rejected';
                    $subs->save();
                }
                CreateActivity::for($order->user, __('Bank transaction status updated to:') . ' ' . __($status), $order->plan->name . ' ' . __('Plan'));

                break;
            default:
                break;
        }

        $order->status = $status;
        $order->save();
    }

    public function checkSubscriptionStatusFromAjax(): JsonResponse
    {
        return response()->json([
            'status' => self::getSubscriptionStatus(),
        ]);
    }

    public static function getSubscriptionStatus(): ?bool
    {
        $gateway = null;
        $activeSub = getCurrentActiveSubscription();

        if ($activeSub) {
            $gateway = $activeSub->paid_with;
        } else {
            $activeSubY = getCurrentActiveSubscriptionYokkasa();
            if ($activeSubY) {
                $gateway = 'yokassa';
            }
        }

        try {
            return GatewaySelector::selectGateway($gateway)::getSubscriptionStatus();
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getSubscriptionDaysLeft()
    {
        $gateway = null;
        $activeSub = getCurrentActiveSubscription();
        if ($activeSub) {
            $gateway = $activeSub->paid_with;
        } else {
            $activeSubY = getCurrentActiveSubscriptionYokkasa();
            if ($activeSubY) {
                $gateway = 'yokassa';
            }
        }

        try {
            return GatewaySelector::selectGateway($gateway)::getSubscriptionDaysLeft();
        } catch (Exception $e) {
            return null;
        }
    }

    public static function cancelActiveSubscription(): null|RedirectResponse|View
    {
        $gateway = null;
        $activeSub = getCurrentActiveSubscription();
        if ($activeSub) {
            $gateway = $activeSub->paid_with;
        } else {
            $activeSubY = getCurrentActiveSubscriptionYokkasa();
            if ($activeSubY) {
                $gateway = 'yokassa';
            }
        }

        try {
            return GatewaySelector::selectGateway($gateway, 'Could not cancel subscription. Please try again. If this error occures again, please update and migrate.')::subscribeCancel();
        } catch (Exception $e) {
            return back()->with(['message' => $e->getMessage(), 'type' => 'error']);
        }
    }

    public static function checkIfTrial()
    {
        $gateway = null;
        $activeSub = getCurrentActiveSubscription();
        if ($activeSub) {
            $gateway = $activeSub->paid_with;
        } else {
            $activeSubY = getCurrentActiveSubscriptionYokkasa();
            if ($activeSubY) {
                $gateway = 'yokassa';
            }
        }

        try {
            if (is_null($gateway)) {
                return false;
            }

            return GatewaySelector::selectGateway($gateway)::checkIfTrial();
        } catch (Exception $e) {
            return back()->with(['message' => $e->getMessage(), 'type' => 'error']);
        }
    }

    public static function getSubscriptionRenewDate()
    {
        $gateway = null;
        $activeSub = getCurrentActiveSubscription();
        if ($activeSub) {
            $gateway = $activeSub->paid_with;
        } else {
            $activeSubY = getCurrentActiveSubscriptionYokkasa();
            if ($activeSubY) {
                $gateway = 'yokassa';
            }
        }

        try {
            return GatewaySelector::selectGateway($gateway)::getSubscriptionRenewDate();
        } catch (Exception $e) {
            return null;
        }
    }

    public static function deletePaymentPlan($id): bool|RedirectResponse|View
    {
        $plan = Plan::where('id', $id)->first();
        if ($plan) {
            $subscriptions = Subscriptions::whereIn('stripe_status', [
                'active',
                'trialing',
                'bank_approved',
                'banktransfer_approved',
                'bank_renewed',
                'free_approved',
                'stripe_approved',
                'paypal_approved',
                'iyzico_approved',
                'paystack_approved',
            ])->where('plan_id', $plan->id)->get();
            if ($subscriptions) {
                foreach ($subscriptions as $subscription) {
                    try {
                        $tmp = GatewaySelector::selectGateway($subscription->paid_with)::cancelSubscribedPlan($subscription, $plan->id);
                    } catch (Exception $e) {
                        return false;
                    }
                }
            }
            // Delete Plan
            $plan->delete();

            return back()->with([
                'message' => __('All subscriptions related to this plan has been cancelled. Plan is deleted.'),
                'type'    => 'success',
            ]);
        }

        return back()->with(['message' => 'Couldn\'t find plan.', 'type' => 'error']);
    }

    public static function saveGatewayProducts($plan, ?Collection $paramGateways = null): null|RedirectResponse|View
    {
        // $typ = $type == "prepaid" ? "o" : "s"; # o => one-time | s => subscription
        // switch ($frequency) {
        //     case 'monthly':
        //         $freq = "m";
        //         break;
        //     case 'yearly':
        //         $freq = "y";
        //         break;
        //     case 'lifetime_monthly':
        //         $freq = "lm";
        //         break;
        //     case 'lifetime_yearly':
        //         $freq = "ly";
        //         break;
        //     default:
        //         $freq = "m";
        //         break;
        // }
        $gateways = $paramGateways ?? Gateways::where('is_active', 1)->get();
        if ($gateways) {
            foreach ($gateways as $gateway) {
                try {
                    GatewaySelector::selectGateway($gateway->code)::saveProduct($plan);
                } catch (Exception $e) {
                    Log::error('saveGatewayProducts(): ' . $e->getMessage());
                }
            }
        } else {
            Log::error('saveGatewayProducts(): Could not find any active gateways!');

            return back()->with(['message' => __('Please enable at least one gateway.'), 'type' => 'error']);
        }

        return null;
    }

    public static function checkForOngoingPayments(): ?bool
    {
        return null;
    }

    public static function checkUnmatchingSubscriptions(): void
    {
        $gateway = null;
        $activeSub = getCurrentActiveSubscription();
        if ($activeSub) {
            $gateway = $activeSub->paid_with;
        } else {
            $activeSubY = getCurrentActiveSubscriptionYokkasa();
            if ($activeSubY) {
                $gateway = 'yokassa';
                $activeSub = $activeSubY;
                $activeSub->paid_with = 'yokassa';
            }
        }
        if ($activeSub) {
            $priceArray = GatewayProducts::all()->pluck('price_id')->toArray();
            // if some plan cancelation happens when appling coupon then add the custom product price_id to custom bilings table
            $customPriceArray = CustomBilingPlans::all()->pluck('custom_plan_price_id')->toArray();
            if (in_array($activeSub->stripe_price, $priceArray, true) || in_array($activeSub->stripe_price, $customPriceArray, true)) {
                // Do nothing. This is what we want.
            } else {
                // Cancel subscription
                try {
                    if ($activeSub->paid_with !== 'yokassa') {
                        $tmp = self::cancelActiveSubscription();
                    }
                } catch (Exception $ex) {
                    Log::error("PaymentProcessController::checkUnmatchingSubscriptions()\n" . $ex->getMessage());
                }
            }
            // Check if active subscription exists on gateway (by stripe_id / subscription id)
            // getSubscriptionStatus function is already called on subscription status file. BUT after functions which gives errors,
            // this needs priority, that's why we add here too. Also this function updates database as cancelled if can't find in gateway
            // there are webhooks for paypal and stripe and paystack
            if ($gateway !== 'paypal' && $gateway !== 'stripe' && $gateway !== 'paystack') {
                $isValid = false;

                try {
                    $isValid = GatewaySelector::selectGateway($gateway)::getSubscriptionStatus();
                } catch (Exception $e) {
                }
            }
            // For some gateways we need to create orders first thats why we have so many Waiting order records. We must clean them.
            $orders = UserOrder::where([
                ['payment_type', '!=', 'banktransfer'], ['status', '=', 'Waiting'], ['user_id', '=', auth()->user()->id],
            ])->get();
            foreach ($orders as $order) {
                $order->delete();
            }
        }
    }

    public static function cancelActiveSubscriptionByAdmin($userId): null|RedirectResponse|View
    {
        $gateway = null;
        $activeSub = getCurrentActiveSubscription($userId);
        if ($activeSub) {
            $gateway = $activeSub->paid_with;
        } else {
            $activeSubY = getCurrentActiveSubscriptionYokkasa($userId);
            if ($activeSubY) {
                $gateway = 'yokassa';
            }
        }

        try {
            $user = User::find($userId);

            return GatewaySelector::selectGateway($gateway, 'Could not cancel subscription. Please try again. If this error occures again, please update and migrate.')::subscribeCancel($user);
        } catch (Exception $e) {
            return back()->with(['message' => $e->getMessage(), 'type' => 'error']);
        }
    }

    public static function assignPlanByAdmin(Request $request): null|RedirectResponse|View
    {
        $request->validate([
            'planID' => 'required',
            'userID' => 'required',
        ]);
        $planID = $request->planID;
        $userID = $request->userID;

        $user = User::find($userID);
        $plan = Plan::where('id', $planID)->first();
        $total = $plan->price;

        $gatewayCode = 'freeservice';
        $tax = 0;
        $taxRate = 0;
        $status = 'free_approved';
        $gateway = Gateways::where('is_active', 1)->first();
        if ($gateway) {
            $taxValue = taxToVal($plan->price, $gateway->tax);
            $total += $taxValue;
            $gatewayCode = $gateway->code;
            $tax = $taxValue;
            $taxRate = $gateway->tax;
            $status = $gateway->code . '_approved';
        }
        $currency = Currency::where('id', $gateway->currency)->first()->code;
        $settings = Setting::getCache();

        try {
            DB::beginTransaction();
            // Create the subscription with the customer ID, price ID, and necessary options.
            $subscription = new Subscriptions;
            $subscription->user_id = $user->id;
            $subscription->name = $planID;
            $subscription->stripe_id = 'FLS-' . strtoupper(Str::random(13));
            $subscription->stripe_status = $status;
            $subscription->stripe_price = 'Not Needed';
            $subscription->quantity = 1;
            $subscription->trial_ends_at = null;
            $subscription->ends_at = $plan->frequency === FrequencyEnum::LIFETIME_MONTHLY->value ? Carbon::now()->addMonths(1) : Carbon::now()->addYears(1);
            $subscription->auto_renewal = 1;
            $subscription->plan_id = $planID;
            $subscription->paid_with = $gatewayCode;
            $subscription->tax_rate = $taxRate;
            $subscription->tax_value = $tax;
            $subscription->total_amount = $total;
            $subscription->save();

            // save the order
            $order = new UserOrder;
            $order->order_id = $subscription->stripe_id;
            $order->plan_id = $planID;
            $order->user_id = $user->id;
            $order->payment_type = $gatewayCode;
            $order->price = $total;
            $order->affiliate_earnings = ($total * $settings->affiliate_commission_percentage) / 100;
            $order->status = 'Success';
            $order->country = $user->country ?? 'Unknown';
            $order->tax_rate = $taxRate;
            $order->tax_value = $tax;
            $order->save();

            self::creditIncreaseSubscribePlan($user, $plan);
            CreateActivity::for($user, __('Subscribed to'), $plan->name . ' ' . __('Plan'), null);
            DB::commit();

            return back()->with([
                'message' => __('The plan has been successfully assigned to the user.'), 'type' => 'success',
            ]);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error($gatewayCode . '-> assignPlan(): ' . $ex->getMessage());

            return back()->with(['message' => Str::before($ex->getMessage(), ':'), 'type' => 'error']);
        }
    }

    public static function assignTokenByAdmin(Request $request): null|RedirectResponse|View
    {
        $request->validate([
            'token'  => 'required',
            'userID' => 'required',
        ]);
        $planID = $request->token;
        $userID = $request->userID;
        $user = User::find($userID);
        $plan = Plan::where('id', $planID)->first();
        $total = $plan->price;
        $gatewayCode = 'freeservice';
        $tax = 0;
        $taxRate = 0;
        $status = 'free_approved';
        $gateway = Gateways::where('is_active', 1)->first();
        if ($gateway) {
            $taxValue = taxToVal($plan->price, $gateway->tax);
            $total += $taxValue;
            $gatewayCode = $gateway->code;
            $tax = $taxValue;
            $taxRate = $gateway->tax;
            $status = $gateway->code . '_approved';
        }

        try {
            DB::beginTransaction();
            $order = new UserOrder;
            $order->order_id = 'ADM-' . strtoupper(Str::random(13));
            $order->plan_id = $plan->id;
            $order->user_id = $user->id;
            $order->type = 'prepaid';
            $order->payment_type = $gatewayCode;
            $order->price = $plan->price;
            $order->affiliate_earnings = 0;
            $order->status = 'Approved';
            $order->country = $user->country ?? 'Unknown';
            $order->tax_rate = 0;
            $order->tax_value = 0;
            $order->save();

            self::creditIncreaseSubscribePlan($user, $plan);
            // sent mail if required here later
            CreateActivity::for($order->user, __('Purchased'), $order->plan->name . ' ' . __('Plan') . ' ' . __('For free'), null);
            DB::commit();

            return back()->with([
                'message' => __('The plan has been successfully assigned to the user.'), 'type' => 'success',
            ]);
        } catch (Exception $th) {
            DB::rollBack();
            Log::error($gatewayCode . '-> subscribe(): ' . $th->getMessage());

            return back()->with(['message' => Str::before($th->getMessage(), ':'), 'type' => 'error']);
        }
    }
}
