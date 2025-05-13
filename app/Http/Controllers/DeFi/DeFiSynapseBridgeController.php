<?php

namespace App\Http\Controllers\DeFi;

use App\Http\Controllers\Controller;

class DeFiSynapseBridgeController extends Controller
{
    public function __invoke()
    {
        return view('panel.user.defi.synapse-bridge');

    }
}
