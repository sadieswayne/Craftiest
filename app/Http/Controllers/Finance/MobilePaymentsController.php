<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\GatewayProducts;
use App\Models\Plan;
use App\Models\RevenueCatProducts;
// use this for subscription table records
use Illuminate\Http\Request;

/**
 * Controls ALL Payment actions of Mobile ( Android & iOS )
 *
 * Also, controls settings of Mobile ( Android & iOS )
 */
class MobilePaymentsController extends Controller
{
    public function mobilePlanIdSettings(Request $request)
    {

        if ($request->isMethod('post')) {

            if ($request->plan_id == 0 || $request->plan_id == '' || $request->plan_id == null) {
                return back()->with(['message' => __('Please select a plan.'), 'type' => 'error']);
            }

            if ($request->plan_name_label == '') {
                return back()->with(['message' => __('Please enter a plan name.'), 'type' => 'error']);
            }

            if ($request->plan_type_label == '') {
                return back()->with(['message' => __('Please enter a plan type.'), 'type' => 'error']);
            }

            if ($request->revenuecat_package_id == '') {
                return back()->with(['message' => __('Please enter a RevenueCat Package ID.'), 'type' => 'error']);
            }

            if ($request->revenuecat_entitlement_id == '') {
                return back()->with(['message' => __('Please enter a RevenueCat Entitlement ID.'), 'type' => 'error']);
            }

            if ($request->revenuecat_google_id == '') {
                return back()->with(['message' => __('Please enter a RevenueCat Google Product ID.'), 'type' => 'error']);
            }

            if ($request->revenuecat_apple_id == '') {
                return back()->with(['message' => __('Please enter a RevenueCat Apple Product ID.'), 'type' => 'error']);
            }

            $plan_id = $request->plan_id;

            $plan = Plan::find($plan_id);

            if (! $plan) {
                return redirect()->back()->with('error', 'Plan not found');
            }

            $gatewayProducts = $plan->gateway_products;

            $revenueCatEntitlementFound = false;

            foreach ($gatewayProducts as $gatewayProduct) {
                if ($gatewayProduct->gateway_code == 'revenuecat') {
                    $revenueCatEntitlementFound = true;
                    $gatewayProduct->product_id = $request->revenuecat_package_id;
                    $gatewayProduct->price_id = $request->revenuecat_entitlement_id;
                    $gatewayProduct->save();

                    break;
                }
            }

            if (! $revenueCatEntitlementFound) {
                $gatewayProduct = new GatewayProducts;
                $gatewayProduct->plan_id = $plan_id;
                $gatewayProduct->plan_name = $request->plan_name_label;
                $gatewayProduct->gateway_code = 'revenuecat';
                $gatewayProduct->gateway_title = 'RevenueCat';
                $gatewayProduct->product_id = $request->revenuecat_package_id;
                $gatewayProduct->price_id = $request->revenuecat_entitlement_id;
                $gatewayProduct->save();
            }

            $revenueCatProductFound = false;

            foreach ($plan->revenuecat_products as $revenueCatProduct) {
                if ($revenueCatProduct != null) {
                    $revenueCatProductFound = true;
                    $revenueCatProduct->entitlement_id = $request->revenuecat_entitlement_id;
                    $revenueCatProduct->package_id = $request->revenuecat_package_id;
                    $revenueCatProduct->google_id = $request->revenuecat_google_id;
                    $revenueCatProduct->apple_id = $request->revenuecat_apple_id;
                    $revenueCatProduct->save();

                    break;
                }
            }

            if (! $revenueCatProductFound) {
                $revenueCatProduct = new RevenueCatProducts;
                $revenueCatProduct->plan_id = $plan_id;
                $revenueCatProduct->gatewayproduct_id = $gatewayProduct->id;
                $revenueCatProduct->entitlement_id = $request->revenuecat_entitlement_id;
                $revenueCatProduct->package_id = $request->revenuecat_package_id;
                $revenueCatProduct->google_id = $request->revenuecat_google_id;
                $revenueCatProduct->apple_id = $request->revenuecat_apple_id;
                $revenueCatProduct->save();
            }

            return back()->with(['message' => __('Plan updated successfully.'), 'type' => 'success']);
        }

        $plans = Plan::with('gatewayProducts')->with('revenuecat_products')->get();

        return view('panel.admin.finance.mobile.index', compact('plans'));
    }
}
