<?php

namespace App\Http\Controllers\Api;

use App\Domains\Entity\EntityStats;
use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppController extends Controller
{
    /**
     * Get the email confirmation setting.
     *
     * @OA\Get(
     *      path="/api/app/email-confirmation-setting",
     *      operationId="getEmailConfirmationSetting",
     *      tags={"App Settings"},
     *      summary="Get email confirmation setting",
     *      description="Get the email confirmation setting from the application settings.",
     *      security={{ "bearerAuth": {} }},
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *              type="object",
     *              example={"emailconfirmation": true},
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     * )
     */
    public function getEmailConfirmationSetting(Request $request)
    {
        $settings = Setting::getCache();
        $data = [
            'emailconfirmation' => ! ((bool) $settings->login_without_confirmation),
        ];

        return response()->json($data);
    }

    /**
     * Get general application settings.
     *
     * @OA\Get(
     *      path="/api/app/get-setting",
     *      operationId="getAppSettings",
     *      tags={"App Settings"},
     *      summary="Get application settings",
     *      description="Get general application settings.",
     *      security={{ "bearerAuth": {} }},
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
    public function getSetting(Request $request)
    {
        $settings = Setting::getCache();
        $settings_two = SettingTwo::getCache();

        // Merge
        $data = array_merge($settings->toArray(), $settings_two->toArray());

        return response()->json($data);

    }

    /**
     * Get usage data of current user
     *
     * @OA\Get(
     *      path="/api/app/usage-data",
     *      operationId="getUsageData",
     *      tags={"App Settings"},
     *      summary="Get usage data of current user",
     *      description="Get usage data and subscription plan details of current user.",
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
    public function getUsageData(Request $request)
    {
        $user = $request->user();
        $userId = $user->id;

        $plan_name = '';
        $planId = '';
        $paid_with = '';

        $planCredits = [];
        $userCredits = $user->entity_credits;

        // Get current active subscription
        $activeSub = getCurrentActiveSubscription($userId);
        if ($activeSub !== null) {
            $paid_with = $activeSub->paid_with;
            $planId = $activeSub->plan_id;
        } else {
            $activeSub = getCurrentActiveSubscriptionYokkasa($userId);
            if ($activeSub !== null) {
                $planId = $activeSub->plan_id;
                $paid_with = 'yokassa';
            }
        }

        $status = getSubscriptionStatus();
        $daysLeft = getSubscriptionDaysLeft() ?? 0;
        $isTrial = checkIfTrial();

        if (! empty($planId)) {
            $plan = Plan::where([['id', '=', $planId]])->first();
            $planCredits = $plan->ai_models;
            $plan_name = $plan->name;
        }

        $usage_percentage = $this->calculateUsagePercent($userCredits, $planCredits);

        $remainingWords = EntityStats::word()->forUser($user)->totalCredits();
        $remainingImages = EntityStats::image()->forUser($user)->totalCredits();

        $data = [
            'subscription_status'     => $status,
            'is_trial'                => $isTrial,
            'days_left'               => $daysLeft,
            'paid_with'               => $paid_with,
            'plan_name'               => $plan_name,
            'total_credits'           => $planCredits,
            'user_credits'            => $userCredits,
            'usage_percentage'        => (float) number_format((float) $usage_percentage, 2, '.', ''),
            'remaining_words'         => $remainingWords,
            'remaining_images'        => $remainingImages,
        ];

        return response()->json($data);
    }

    private function calculateUsagePercent(array $userCredits, array $planCredits): float|int
    {
        $remainingPercent = 0;
        foreach ($planCredits as $engineKey => $entities) {
            foreach ($entities as $entityKey => $entity) {
                $planCredit = $entity['credit'];
                if (isset($userCredits[$engineKey][$entityKey]['credit'])) {
                    $userCredit = $userCredits[$engineKey][$entityKey]['credit'];
                    $remainingPercent += $userCredit / ($planCredit === 0 or $planCredit === null ? 1 : $planCredit);
                }
            }
        }
        $remainingPercent /= 100;

        return $remainingPercent;
    }

    /**
     * Get default currency
     *
     * @OA\Get(
     *      path="/api/app/currency/{id?}",
     *      operationId="getCurrency",
     *      tags={"App Settings"},
     *      summary="Get default currency",
     *      description="Returns default currency if id is not provided, else returns currency by id. Use 'all' to get all currencies.",
     *      security={{ "passport": {} }},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Id of currency or 'all' or null to get default currency.",
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
     *          response=404,
     *          description="No currency found.",
     *      ),
     * )
     */
    public function getCurrency(Request $request, $id = null)
    {

        if ($id == 'all') {
            $currency = Currency::all();

            return response()->json($currency);
        } elseif ($id != null) {
            $currency = Currency::where([['id', '=', $id]])->first();

            return response()->json($currency);
        }

        $currencyId = Setting::getCache()->default_currency;
        if ($currencyId != null) {
            $currency = Currency::where([['id', '=', $currencyId]])->first();

            return response()->json($currency);
        }

        return response()->json(['message' => 'No currency found.'], 404);

    }

    /**
     * Gets app logo
     *
     * @OA\Get(
     *      path="/api/auth/logo",
     *      operationId="getLogo",
     *      tags={"General (Helpers)"},
     *      summary="Get logo path",
     *      description="Returns logo path from settings.",
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
     *          description="No logo found.",
     *      ),
     * )
     */
    public function getLogo(Request $request)
    {

        $settings = Setting::getCache();

        if ($settings->logo_path != null) {
            return response()->json(custom_theme_url($settings->logo_path));
        }

        if ($settings->logo_sticky_path != null) {
            return response()->json(custom_theme_url($settings->logo_sticky_path));
        }

        if ($settings->logo_2x_path != null) {
            return response()->json(custom_theme_url($settings->logo_2x_path));
        }

        if ($settings->logo_sticky_2x_path != null) {
            return response()->json(custom_theme_url($settings->logo_sticky_2x_path));
        }

        if ($settings->logo_collapsed_path != null) {
            return response()->json(custom_theme_url($settings->logo_collapsed_path));
        }

        return response()->json(['error' => 'No logo found.'], 404);

    }
}
