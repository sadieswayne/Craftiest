<?php

namespace App\Http\Controllers\DeFi;

use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Services\DeFi\DeFiMyPortfolioService;
use Illuminate\Http\Request;

class DeFiMyPortfolioController extends Controller
{
    public function __construct(
        public DeFiMyPortfolioService $service
    ) {}

    public function __invoke(Request $request)
    {
        $setting = $request->user()->getAttribute('defi_setting');

        $token = $request->get('wallet_id') ?: data_get($setting, 'portfolio.wallet_id');

        if ($token) {

            $setting['portfolio']['wallet_id'] = $token;

            $request->user()->update(['defi_setting' => $setting]);

            $data = $this->service->data($token);

            if (isset($data['assets'])) {
                $data['assets'] = Helper::sorting(
                    $data['assets'],
                    request('column'),
                    request('direction', 'asc')
                );
            }

            return view('panel.user.defi.my-portfolio', compact('data', 'token'));
        }

        return to_route('dashboard.de-fi.setting', ['route' => $request->route()->getName()]);
    }
}
