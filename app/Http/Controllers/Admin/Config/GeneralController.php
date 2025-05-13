<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class GeneralController extends Controller
{
    public function index(): View
    {
        return view('panel.admin.config.home');
    }
}
