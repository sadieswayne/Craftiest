<?php

namespace App\Http\Controllers\DeFi;

use App\Http\Controllers\Controller;
use App\Services\DeFi\DeFiMarketDataService;

class DeFiMarketDataShowController extends Controller
{
    public function __construct(public DeFiMarketDataService $service) {}

    public function __invoke(string $slug)
    {
        $data = $this->service->single($slug);

        return view('panel.user.defi.market-data.single', compact('data'));
    }
}
