<?php

namespace App\Http\Controllers\DeFi;

use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Services\DeFi\DeFiMarketDataService;

class DeFiMarketDataController extends Controller
{
    public function __construct(public DeFiMarketDataService $service) {}

    public function __invoke()
    {
        $data = Helper::sorting(
            $this->service->data(),
            request('column'),
            request('direction', 'asc')
        );

        return view('panel.user.defi.market-data.index', compact('data'));
    }
}
