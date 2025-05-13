<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Http\Middleware\Custom\FinanceLicenseMiddleware;
use App\Models\Plan;
use Illuminate\Contracts\View\View;

class TokenPackPlanController extends Controller
{
    public function __construct()
    {
        $this->middleware(FinanceLicenseMiddleware::class);
    }

    public function create(): View
    {
        return view('panel.admin.finance.plan.form', [
            'method'       => 'POST',
            'title'        => trans('Create Token Pack Plan'),
            'item'         => new Plan,
            'subscription' => false,
        ]);
    }

    public function edit(Plan $tokenPackPlan): View
    {
        return view('panel.admin.finance.plan.form', [
            'method'       => 'POST',
            'title'        => trans('Edit Token Pack Plan'),
            'item'         => $tokenPackPlan,
            'subscription' => false,
        ]);
    }
}
