<?php

namespace App\Helpers\Classes;

use App\Models\Plan;
use App\Services\Common\MenuService;
use Illuminate\Support\Facades\Auth;

class PlanHelper
{
    public static function userPlanAiModel(): ?string
    {
        return self::userPlan()?->default_ai_model;
    }

    public static function userPlan()
    {
        if (Auth::check()) {

            $subscription = getCurrentActiveSubscription(Auth::id());

            if ($subscription) {
                return Plan::query()->where('id', $subscription->plan_id)->first();
            }
        }

        return null;
    }

    public static function planMenuCheck($plan, ?string $key = null): bool
    {
        if (! $plan) {
            return true;
        }

        if (! $key) {
            return true;
        }

        $dataAiTools = array_map(function ($item) {
            return $item['key'];
        }, MenuService::planAiToolsMenu());

        $dataFeature = array_map(function ($item) {
            return $item['key'];
        }, MenuService::planFeatureMenu());

        $data = array_merge($dataAiTools, $dataFeature);

        if (! in_array($key, $data)) {
            return true;
        }

        $plan_ai_tools = (array) $plan->plan_ai_tools;

        $plan_features = (array) $plan->plan_features;

        $checkArray = array_merge($plan_ai_tools, $plan_features);

        if ($checkArray) {
            return in_array($key, $checkArray);
        }

        return false;
    }
}
