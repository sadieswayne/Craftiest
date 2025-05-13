<?php

namespace App\Http\Controllers\DeFi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeFiContractAnalysesController extends Controller
{
    public function __invoke(Request $request)
    {
        $setting = $request->user()->getAttribute('defi_setting');

        $token = $request->get('wallet_id') ?: data_get($setting, 'contract.analyses_wallet', '');

        $setting['contract']['analyses_wallet'] = $token;

        $request->user()->update(['defi_setting' => $setting]);

        return view('panel.user.defi.contract-analyses', compact('token'));
    }
}
