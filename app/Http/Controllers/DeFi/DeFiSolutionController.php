<?php

namespace App\Http\Controllers\DeFi;

use App\Http\Controllers\Controller;

class DeFiSolutionController extends Controller
{
    public function __invoke()
    {
        return view('panel.user.defi.index');
    }
}
