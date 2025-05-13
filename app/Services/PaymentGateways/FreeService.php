<?php

namespace App\Services\PaymentGateways;

use App\Actions\CreateActivity;
use App\Actions\EmailPaymentConfirmation;
use App\Enums\Plan\FrequencyEnum;
use App\Models\GatewayProducts;
use App\Models\Plan;
// use App\Models\Subscriptions;
use App\Models\User;
use App\Models\UserOrder;
use App\Services\PaymentGateways\Contracts\CreditUpdater;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Cashier\Subscription as Subscriptions;

/**
 * Base functions foreach payment gateway
 *
 * @param saveAllProducts
 * @param saveProduct ($plan)
 * @param subscribe ($plan)
 * @param subscribeCheckout ($planID, $orderID)
 * @param prepaid ($plan)
 * @param prepaidCheckout ($planID, $orderID)
 * @param getSubscriptionStatus ($incomingUserId = null)
 * @param getSubscriptionDaysLeft
 * @param subscribeCancel
 * @param checkIfTrial
 * @param getSubscriptionRenewDate
 * @param cancelSubscribedPlan ($subscription, $planId)
 */
class FreeService
{
    use CreditUpdater;

    protected static $GATEWAY_CODE = 'freeservice';

    protected static $GATEWAY_NAME = 'Free';

    public static function saveProduct($plan)
    {
        try {
            $productData = GatewayProducts::where(['plan_id' => $plan->id, 'gateway_code' => self::$GATEWAY_CODE])->first();
            if ($productData == null) {
                $product = new GatewayProducts;
                $product->plan_id = $plan->id;
                $product->plan_name = $plan->name;
                $product->gateway_code = self::$GATEWAY_CODE;
                $product->gateway_title = self::$GATEWAY_NAME;
                $product->product_id = 'FPP-' . strtoupper(Str::random(13));
                $product->price_id = 'Not Needed';
                $product->save();
            } else {
                $productData->plan_name = $plan->name;
                $productData->save();
            }
        } catch (Exception $ex) {
            Log::error(self::$GATEWAY_CODE . '-> saveProduct(): ' . $ex->getMessage());

            return back()->with(['message' => $ex->getMessage(), 'type' => 'error']);
        }
    }

    public static function subscribe($plan)
    {
        if ($plan->price != 0) {
            abort(404);
        }

        try {
            $user = auth()->user();
            $product = GatewayProducts::where(['plan_id' => $plan->id, 'gateway_code' => self::$GATEWAY_CODE])->first();
            if ($product == null) {
                self::saveProduct($plan);
            }
            $order_id = 'FPS-' . strtoupper(Str::random(13));

            return view('panel.user.finance.subscription.' . self::$GATEWAY_CODE, compact('plan', 'order_id'));
        } catch (Exception $th) {
            Log::error(self::$GATEWAY_CODE . '-> subscribe(): ' . $th->getMessage());

            return back()->with(['message' => Str::before($th->getMessage(), ':'), 'type' => 'error']);
        }
    }

    public static function subscribeCheckout(Request $request, $referral = null)
    {
        $planID = $request->input('planID', null);
        $orderID = $request->input('orderID', null);
        $couponID = $request->input('couponID', null);

        $user = auth()->user();
        $plan = Plan::find($planID) ?? abort(404);
        $total = $plan->price;

        switch ($plan->frequency) {
            case FrequencyEnum::MONTHLY->value:
                $previousPeriod = now()->subMonth();

                break;
            case FrequencyEnum::YEARLY->value:
                $previousPeriod = now()->subYear();

                break;
            case FrequencyEnum::LIFETIME_MONTHLY->value:
                $previousPeriod = now()->subMonth();

                break;
            case FrequencyEnum::LIFETIME_YEARLY->value:
                $previousPeriod = now()->subYear();

                break;
            default:
                $previousPeriod = now()->subMonth();

                break;
        }
        $existingOrder = UserOrder::where('user_id', $user->id)
            ->where('type', 'subscription')
            ->where('status', 'Approved')
            ->where('created_at', '>=', $previousPeriod)
            ->first();
        if ($existingOrder) {
            return back()->with(['message' => __('You cannot subscribe to another free plan during the current period.'), 'type' => 'error']);
        }

        try {
            DB::beginTransaction();
            $subscription = new Subscriptions;
            $subscription->user_id = $user->id;
            $subscription->name = $plan->id;
            $subscription->stripe_id = $orderID;
            $subscription->stripe_status = 'free_approved';
            $subscription->stripe_price = 'Not Needed';
            $subscription->quantity = 1;
            $subscription->trial_ends_at = null;
            switch ($plan->frequency) {
                case FrequencyEnum::MONTHLY->value:
                    $subscription->ends_at = \Carbon\Carbon::now()->addMonths(1);
                    $subscription->auto_renewal = 1;

                    break;
                case FrequencyEnum::YEARLY->value:
                    $subscription->ends_at = \Carbon\Carbon::now()->addYears(1);
                    $subscription->auto_renewal = 1;

                    break;
                case FrequencyEnum::LIFETIME_MONTHLY->value:
                    $subscription->ends_at = \Carbon\Carbon::now()->addMonths(1); // ends each month but auto renewing without payment reqs
                    $subscription->auto_renewal = 1;

                    break;
                case FrequencyEnum::LIFETIME_YEARLY->value:
                    $subscription->ends_at = \Carbon\Carbon::now()->addYears(1); // ends each year but auto renewing without payment reqs
                    $subscription->auto_renewal = 1;

                    break;
                default:
                    $subscription->ends_at = \Carbon\Carbon::now()->addDays(30);
                    $subscription->auto_renewal = 1;

                    break;
            }
            $subscription->tax_rate = 0;
            $subscription->tax_value = 0;
            $subscription->coupon = null;
            $subscription->total_amount = $total;
            $subscription->plan_id = $plan->id;
            $subscription->paid_with = self::$GATEWAY_CODE;
            $subscription->save();

            $order = new UserOrder;
            $order->order_id = $orderID;
            $order->plan_id = $plan->id;
            $order->user_id = $user->id;
            $order->payment_type = self::$GATEWAY_CODE;
            $order->price = $total;
            $order->affiliate_earnings = 0;
            $order->status = 'Approved';
            $order->type = 'subscription';
            $order->country = $user->country ?? 'Unknown';
            $order->tax_rate = 0;
            $order->tax_value = 0;
            $order->save();

            self::creditIncreaseSubscribePlan($user, $plan);

            // sent mail if required here later
            CreateActivity::for($order->user, __('Purchased'), $order->plan->name . ' ' . __('Plan') . ' ' . __('For free'));
            EmailPaymentConfirmation::create($user, $plan)->send();
        } catch (Exception $th) {
            DB::rollBack();
            Log::error(self::$GATEWAY_CODE . '-> subscribe(): ' . $th->getMessage());

            return back()->with(['message' => Str::before($th->getMessage(), ':'), 'type' => 'error']);
        }
        DB::commit();

        return redirect()->route('dashboard.user.payment.succesful')->with([
            'message' => __('Thank you for your purchase. Enjoy your remaining words and images.'),
            'type'    => 'success',
        ]);
    }

    public static function prepaid($plan)
    {
        if ($plan->price != 0) {
            abort(404);
        }

        try {
            $user = auth()->user();
            $product = GatewayProducts::where(['plan_id' => $plan->id, 'gateway_code' => self::$GATEWAY_CODE])->first();
            if ($product == null) {
                self::saveProduct($plan);
            }
            $existingPrepaidOrder = UserOrder::where('user_id', $user->id)
                ->where('type', 'prepaid')
                ->where('status', 'Approved')
                ->first();
            if ($existingPrepaidOrder) {
                $order_id = $existingPrepaidOrder->order_id;
            } else {
                $order_id = 'FPO-' . strtoupper(Str::random(13));
            }

            return view('panel.user.finance.prepaid.' . self::$GATEWAY_CODE, compact('plan', 'order_id', 'existingPrepaidOrder'));
        } catch (Exception $th) {
            Log::error(self::$GATEWAY_CODE . '-> prepaid(): ' . $th->getMessage());

            return back()->with(['message' => Str::before($th->getMessage(), ':'), 'type' => 'error']);
        }
    }

    public static function prepaidCheckout(Request $request, $referral = null)
    {
        $planID = $request->input('planID', null);
        $orderID = $request->input('orderID', null);
        $couponID = $request->input('couponID', null);

        $user = auth()->user();
        $plan = Plan::find($planID) ?? abort(404);
        $total = $plan->price;

        $existingPrepaidOrder = UserOrder::where('user_id', $user->id)
            ->where('order_id', $orderID)
            ->where('type', 'prepaid')
            ->where('status', 'Approved')
            ->first();
        if ($existingPrepaidOrder) {
            return back()->with(['message' => __('This pack alredy purchased'), 'type' => 'error']);
        }

        try {
            DB::beginTransaction();
            $order = new UserOrder;
            $order->order_id = $orderID;
            $order->plan_id = $plan->id;
            $order->user_id = $user->id;
            $order->type = 'prepaid';
            $order->payment_type = self::$GATEWAY_CODE;
            $order->price = $plan->price;
            $order->affiliate_earnings = 0;
            $order->status = 'Approved';
            $order->country = $user->country ?? 'Unknown';
            $order->tax_rate = 0;
            $order->tax_value = 0;
            $order->save();

            self::creditIncreaseSubscribePlan($user, $plan);
            // sent mail if required here later
            CreateActivity::for($order->user, __('Purchased'), $order->plan->name . ' ' . __('Plan') . ' ' . __('For free'));
            EmailPaymentConfirmation::create($user, $plan)->send();
        } catch (Exception $th) {
            DB::rollBack();
            Log::error(self::$GATEWAY_CODE . '-> subscribe(): ' . $th->getMessage());

            return back()->with(['message' => Str::before($th->getMessage(), ':'), 'type' => 'error']);
        }
        DB::commit();

        return redirect()->route('dashboard.user.payment.succesful')->with([
            'message' => __('Thank you for your purchase. Enjoy your remaining words and images.'),
            'type'    => 'success',
        ]);
    }

    public static function getSubscriptionStatus($incomingUserId = null)
    {
        if ($incomingUserId != null) {
            $user = User::where('id', $incomingUserId)->first();
        } else {
            $user = Auth::user();
        }
        $sub = getCurrentActiveSubscription($user->id);
        if ($sub != null) {
            return true;
        }

        return false;
    }

    public static function getSubscriptionDaysLeft()
    {
        $user = Auth::user();
        $sub = getCurrentActiveSubscription($user->id);
        if ($sub) {
            return \Carbon\Carbon::now()->diffInDays($sub->ends_at);
        } else {
            Log::error('getSubscriptionDaysLeft()');

            return 0;
        }
    }

    public static function subscribeCancel($internalUser = null)
    {
        $user = $internalUser ?? Auth::user();
        $activeSub = getCurrentActiveSubscription($user->id);
        if ($activeSub != null) {
            $plan = Plan::where('id', $activeSub->plan_id)->first();

            self::creditDecreaseCancelPlan($user, $plan);

            $activeSub->stripe_status = 'free_canceled';
            $activeSub->save();

            CreateActivity::for($user, 'cancelled', $plan->name);
            if ($internalUser != null) {
                return back()->with(['message' => __('User subscription is cancelled succesfully.'), 'type' => 'success']);
            }

            return redirect()->route('dashboard.user.index')->with(['message' => __('Your subscription is cancelled succesfully.'), 'type' => 'success']);
        }

        return back()->with(['message' => __('Could not find active subscription. Nothing changed!'), 'type' => 'error']);
    }

    public static function checkIfTrial()
    {
        // there is no trail in free
        return false;
    }

    public static function getSubscriptionRenewDate()
    {
        $user = Auth::user();
        $activeSub = getCurrentActiveSubscription($user->id);

        return \Carbon\Carbon::parse($activeSub->ends_at)->format('F jS, Y');
    }

    public static function cancelSubscribedPlan($subscription, $planId)
    {
        try {
            $order = UserOrder::where('order_id', $subscription->stripe_id)->first();

            /**
             * @var Plan $plan
             */
            $plan = $order->plan;

            /**
             * @var User $user
             */
            $user = $order->user;

            self::creditDecreaseCancelPlan($user, $plan);

            $subscription->stripe_status = 'free_canceled';
            $subscription->save();
            // sent mail if required here later
            CreateActivity::for($order->user, __('Subscription canceled due to plan deletion.'), $order->plan->name . ' ' . __('Plan'));

            return true;
        } catch (Exception $th) {
            Log::error(self::$GATEWAY_CODE . ' cancelSubscribedPlan(): ' . $th->getMessage() . "\n------------------------\n");

            return false;
        }
    }

    public static function handleWebhook(Request $request)
    {
        return response()->json(['success' => true]);
    }
}
