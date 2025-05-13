<?php

namespace App\Http\Controllers;

use App\Enums\Plan\FrequencyEnum;
use App\Enums\Plan\TypeEnum;
use App\Models\Gateways;
use App\Models\OpenAIGenerator;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PrivatePlanController extends Controller
{
    public function index(Request $request, $url): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('register');
        }

        $activeGateways = Gateways::query()->where('is_active', 1)->get();

        $is_active_gateway = $activeGateways->count() > 0 ? 1 : 0;

        // Check if any subscription is active
        $userId = Auth::id();
        $activeSub = getCurrentActiveSubscription($userId) ?? getCurrentActiveSubscriptionYokkasa($userId);
        $activesubid = $activeSub ? $activeSub->plan_id : 0;

        $lastPrivateDate = false;
        $maxSubscribe = false;

        $plans = Plan::query()
            ->where('active', 1)
            ->where('hidden', true)
            ->where('hidden_url', $request->fullUrl())
            ->get();

        if (! is_null($plans->first())) {
            $plan = $plans->first();
            if (isset($plan->last_date) && ($plan->last_date <= date('Y-m-d'))) {
                $lastPrivateDate = true;
            }

            if (isset($plan->max_subscribe) && ($plan->max_subscribe == '0')) {
                $maxSubscribe = true;
            }
        }

        $plansSubscriptionMonthly = $plans->where('type', TypeEnum::SUBSCRIPTION->value)
            ->where('frequency', FrequencyEnum::MONTHLY->value);

        $plansSubscriptionLifetime = $plans->where('type', TypeEnum::SUBSCRIPTION->value)
            ->whereIn('frequency', [FrequencyEnum::LIFETIME_YEARLY->value, FrequencyEnum::LIFETIME_MONTHLY->value]);

        $plansSubscriptionAnnual = $plans->where('type', TypeEnum::SUBSCRIPTION->value)
            ->where('frequency', FrequencyEnum::YEARLY->value);

        $prepaidplans = $plans->where('type', TypeEnum::TOKEN_PACK->value);

        $openAiList = OpenAIGenerator::all();

        $currency = currency();

        return view('panel.user.finance.subscriptionPlans', compact(
            'plansSubscriptionMonthly',
            'plansSubscriptionLifetime',
            'plansSubscriptionAnnual',
            'prepaidplans',
            'openAiList',
            'is_active_gateway',
            'activeGateways',
            'activesubid',
            'currency',
            'lastPrivateDate',
            'maxSubscribe',
        ));
    }
}
