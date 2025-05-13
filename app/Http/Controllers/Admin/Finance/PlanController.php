<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Finance\PaymentProcessController;
use App\Http\Middleware\Custom\FinanceLicenseMiddleware;
use App\Models\Plan;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use RachidLaasri\LaravelInstaller\Repositories\ApplicationStatusRepositoryInterface;

class PlanController extends Controller
{
    public function __construct(
        public ApplicationStatusRepositoryInterface $applicationStatusRepository
    ) {
        $this->middleware(FinanceLicenseMiddleware::class, ['except' => ['index']]);
    }

    public function index(): View
    {
        return view('panel.admin.finance.plan.index', [
            'gatewayError' => false,
            'setting'      => Setting::query()->first(),
            'plans'        => Plan::query()->orderByDesc('updated_at')
                ->select(['id', 'name', 'type', 'frequency', 'active', 'updated_at', 'price'])
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('panel.admin.finance.plan.form', [
            'method'       => 'POST',
            'title'        => trans('Create Plan'),
            'item'         => new Plan,
            'subscription' => true,
        ]);
    }

    public function edit(Plan $plan): View
    {

        return view('panel.admin.finance.plan.form', [
            'method'       => 'POST',
            'title'        => trans('Edit Plan'),
            'item'         => $plan,
            'subscription' => true,
        ]);
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        PaymentProcessController::deletePaymentPlan($plan->id);

        return redirect()->route('dashboard.admin.finance.plan.index');
    }
}
