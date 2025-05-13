<?php

namespace App\Http\Controllers\Admin\Config;

use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinanceController extends Controller
{
    protected $settings;

    public function __construct()
    {
        $this->settings = Setting::query()->first();
    }

    public function index(): View
    {
        return view('panel.admin.config.finance');
    }

    public function store(Request $request): RedirectResponse
    {
        if (Helper::appIsDemo()) {
            return back()->with(['message' => __('This feature is disabled in Demo version.'), 'type' => 'error']);
        }

        $this->settings->update([
            'affiliate_minimum_withdrawal'    => $request->get('affiliate_minimum_withdrawal'),
            'affiliate_commission_percentage' => $request->get('affiliate_commission_percentage'),
            'invoice_currency'                => $request->get('invoice_currency'),
            'invoice_name'                    => $request->get('invoice_name'),
            'invoice_website'                 => $request->get('invoice_website'),
            'invoice_address'                 => $request->get('invoice_address'),
            'invoice_city'                    => $request->get('invoice_city'),
            'invoice_state'                   => $request->get('invoice_state'),
            'invoice_country'                 => $request->get('invoice_country'),
            'invoice_phone'                   => $request->get('invoice_phone'),
            'invoice_postal'                  => $request->get('invoice_postal'),
            'invoice_vat'                     => $request->get('invoice_vat'),
            'default_country'                 => $request->get('default_country'),
            'default_currency'                => $request->get('default_currency'),
        ]);

        setting(
            [
                'onetime_commission'  => $request->has('onetime_commission') ? 1 : 0,
            ]
        )->save();

        Setting::forgetCache();

        return back()->with(['message' => 'Updated Successfully.', 'type' => 'success']);
    }
}
