<?php

namespace App\Http\Controllers;

class PremiumSupportController extends Controller
{
    public function __invoke()
    {
        return view('premium-support.index');
    }
}
