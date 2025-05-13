<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserOrder;
use App\Services\GatewaySelector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class PaymentApiController extends Controller
{
    /**
     * Get subscription plan of current user
     *
     * @OA\Get(
     *      path="/api/payment/",
     *      operationId="getCurrentPlan",
     *      tags={"Payments"},
     *      summary="Get subscription plan of current user",
     *      description="Get subscription plan details of current user.",
     *      security={{ "passport": {} }},
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="No active subscription found",
     *      ),
     * )
     */
    public function getCurrentPlan(Request $request)
    {

        $userId = Auth::user()->id;
        $planId = '';

        // Get current active subscription
        $activeSub = getCurrentActiveSubscription($userId);
        if ($activeSub != null) {
            $planId = $activeSub->plan_id;
        } else {
            $activeSub = getCurrentActiveSubscriptionYokkasa($userId);
            if ($activeSub != null) {
                $planId = $activeSub->plan_id;
            }
        }

        if ($planId != '') {
            $plan = Plan::where([['id', '=', $planId]])->first();

            return response()->json($plan);
        }

        return response()->json(['message' => 'No active subscription found.'], 404);

    }

    /*
        This function converts the response from the gateways to json response.
        That is ;
            - if the response is success, it returns 200 with message
            - if the response is error, it returns 412 with message

        Gateway returns as :
            return back()->with(['message' => __('Your subscription is cancelled succesfully.'), 'type' => 'success']);
            return back()->with(['message' => __('Your subscription could not be cancelled.'), 'type' => 'error']);

        But api must return :
            return response()->json(['message' => __('Your subscription is cancelled succesfully.'), 'type' => 'success'], 200);
            return response()->json(['message' => __('Your subscription could not be cancelled.'), 'type' => 'error'], 412);

    */
    private function convertBackWithToResponseJson($originalContent)
    {
        $type = $originalContent['type'];

        if ($type == 'success') {
            return response()->json(['message' => $originalContent['message']], 200);
        } else {
            return response()->json(['message' => $originalContent['message']], 412);
        }
    }

    /**
     * Cancel current subscription plan of current user
     *
     * @OA\Get(
     *      path="/api/payment/subscriptions/cancel-current",
     *      operationId="cancelActiveSubscription",
     *      tags={"Payments"},
     *      summary="Cancel current subscription plan of current user",
     *      description="Cancel current subscription plan details of current user.",
     *      security={{ "passport": {} }},
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     * )
     */
    public function cancelActiveSubscription(Request $request)
    {

        $user = Auth::user();
        $userId = $user->id;
        // Get current active subscription
        $activeSub = getCurrentActiveSubscription($userId);
        if ($activeSub == null) {
            $activeSub_yokassa = getCurrentActiveSubscriptionYokkasa($userId);
            if ($activeSub_yokassa == null) {
                return response()->json(['message' => 'No active subscription found.'], 404);
            }
            $gatewayCode = 'yokassa';
        } else {
            $gatewayCode = $activeSub->paid_with;
        }

        Log::info('Cancelling subscription for user: ' . $user->id . ' with gateway: ' . $gatewayCode . ' via API.');

        if ($gatewayCode == 'revenuecat') {
            // / Mobile gateways do NOT allow developers to cancel subscriptions so we redirect them to their store.
            // / Update will take 4-5 hours to reflect on the RevenueCat dashboard.
            return response()->json(['message' => 'This subscription can be cancelled from your mobile store only!'], 412);
        }

        try {
            return self::convertBackWithToResponseJson(GatewaySelector::selectGateway($gatewayCode)::subscribeCancel());
        } catch (Throwable $th) {
            return response()->json(['message' => 'Could not cancel subscription. Please try again. If this error occures again, please update and migrate.'], 404);
        }
    }

    /**
     * Get all plans
     *
     * @OA\Get(
     *      path="/api/payment/plans/{plan_id?}",
     *      operationId="plans",
     *      tags={"Payments"},
     *      summary="Get all plans.",
     *      description="Get all plans.",
     *      security={{ "passport": {} }},
     *
     *      @OA\Parameter(
     *          name="plan_id",
     *          description="Id of plan to get details of.",
     *          in="path",
     *          required=false,
     *
     *          @OA\Schema(type="string"),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     * )
     */
    public function plans(Request $request, $plan_id = null)
    {

        if ($request->has('plan_id') || $plan_id != null) {
            $id = $request->plan_id != null ? $request->plan_id : $plan_id;
            $plan = Plan::with('gatewayProducts')->where([['id', '=', $id]])->first();

            return response()->json($plan);
        }

        $plans = Plan::with('gatewayProducts')->get();

        return response()->json($plans);

    }

    /**
     * Get all orders
     *
     * @OA\Get(
     *      path="/api/payment/orders/{order_id?}",
     *      operationId="orders",
     *      tags={"Payments"},
     *      summary="Get all orders.",
     *      description="Get all orders. If order_id is provided, then it will return details of that order.",
     *      security={{ "passport": {} }},
     *
     *      @OA\Parameter(
     *          name="order_id",
     *          description="Id of order to get details of.",
     *          in="path",
     *          required=false,
     *
     *          @OA\Schema(type="string"),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Permission Denied",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Order not found",
     *      ),
     * )
     */
    public function orders(Request $request, $order_id = null)
    {

        $user = Auth::user();

        if ($request->has('order_id') || $order_id != null) {
            $id = $request->order_id != null ? $request->order_id : $order_id;
            $order = UserOrder::where([['id', '=', $id]])->first();
            if ($order == null) {
                return response()->json(['message' => 'Order not found.'], 404);
            }

            if ($order->user_id != $user->id && ! Auth::user()->isAdmin() && $order->plan_id != null) {
                return response()->json(['message' => 'User does not have permission.'], 403);

            }

            return response()->json($order);
        }

        // $list = $user->orders;
        $list = UserOrder::where([['user_id', '=', $user->id], ['plan_id', '!=', null]])->orderBy('created_at', 'desc')->get();

        return response()->json($list);

    }

    /**
     * Trigger RevenueCat to check subscription / token pack status
     *
     * @OA\Get(
     *      path="/api/payment/check-revenue-cat",
     *      operationId="checkRevenueCat",
     *      tags={"Payments"},
     *      summary="Trigger RevenueCat to check subscription / token pack status.",
     *      description="Trigger RevenueCat to check subscription / token pack status.",
     *      security={{ "passport": {} }},
     *
     *      @OA\Response(
     *          response=200,
     *          description="Active subscription found",
     *
     *          @OA\JsonContent(
     *              type="object",
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="No active subscription found",
     *      ),
     *      @OA\Response(
     *          response=412,
     *          description="Error occured. Custom Message displayed",
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Error occured. Custom Message displayed",
     *      ),
     * )
     */
    public function checkRevenueCat(Request $request)
    {
        return GatewaySelector::selectGateway('revenuecat')::getSubscriptionStatus($fromApi = true);
    }
}
