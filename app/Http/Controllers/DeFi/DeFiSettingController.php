<?php

namespace App\Http\Controllers\DeFi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DeFi\DeFiSettingRequest;
use Auth;
use Route;

class DeFiSettingController extends Controller
{
    public function __invoke()
    {
        $setting = Auth::user()->getAttribute('defi_setting');

        return view('panel.user.defi.setting', [
            'portfolio' => [
                'wallet_name' => data_get($setting, 'portfolio.wallet_name'),
                'wallet_id'   => data_get($setting, 'portfolio.wallet_id'),
            ],
            'contract' => [
                'analyses_wallet' => data_get($setting, 'contract.analyses_wallet'),
            ],
        ]);
    }

    public function update(DeFiSettingRequest $request)
    {
        $validate = $request->validated();

        Auth::user()->update([
            'defi_setting' => $validate,
        ]);

        $route = $request->get('route');

        if ($route && Route::has($route)) {
            return to_route($route)->with([
                'type'    => 'success',
                'message' => trans('Setting update'),
            ]);
        }

        return back()->with([
            'type'    => 'success',
            'message' => trans('Setting update'),
        ]);
    }
}
