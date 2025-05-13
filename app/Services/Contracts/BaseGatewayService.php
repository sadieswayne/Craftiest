<?php

namespace App\Services\Contracts;

use App\Models\User;
use Illuminate\Http\Request;

interface BaseGatewayService
{
    public static function saveAllProducts();

    public static function saveProduct($plan);

    public static function subscribe($plan);

    public static function subscribeCheckout(Request $request, $referral = null);

    public static function prepaidCheckout(Request $request, $referral = null);

    public static function prepaid($plan);

    public static function subscribeCancel(?User $internalUser = null);

    public static function cancelSubscribedPlan($subscription, $planId);

    public static function checkIfTrial();

    public static function getSubscriptionRenewDate();

    public static function getSubscriptionStatus($incomingUserId = null);

    public static function getSubscriptionDaysLeft();

    public static function handleWebhook(Request $request);
}
