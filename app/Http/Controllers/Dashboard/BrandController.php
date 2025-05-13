<?php

namespace App\Http\Controllers\Dashboard;

use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Brand\BrandRequest;
use App\Models\Company;
use App\Models\Product;
use App\Services\User\BrandService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class BrandController extends Controller
{
    public function __construct(public BrandService $service) {}

    public function index()
    {
        return view('panel.user.companies.list', [
            'list' => Company::query()->where('user_id', Auth::id())->orderBy('name', 'asc')->get(),
        ]);
    }

    public function delete(Company $brand): RedirectResponse
    {
        if (Helper::appIsDemo()) {
            return back()->with(['message' => __('This feature is disabled in Demo version.'), 'type' => 'error']);
        }

        Product::query()->where('company_id', $brand->getKey())->delete();

        $brand->delete();

        return back()->with(['message' => __('Deleted Successfully'), 'type' => 'success']);
    }

    public function create(): View
    {
        return view('panel.user.companies.form', [
            'item'   => new Company,
            'action' => route('dashboard.user.brand.store'),
            'method' => 'POST',
        ]);
    }

    public function edit(Company $brand)
    {
        return view('panel.user.companies.form', [
            'item'   => $brand,
            'action' => route('dashboard.user.brand.update', $brand->id),
            'method' => 'PUT',
        ]);
    }

    public function store(BrandRequest $request): JsonResponse
    {
        if (Helper::appIsDemo()) {
            return response()->json(__('This feature is disabled in Demo version.'), 419);
        }

        $this->service->create($request);

        return response()->json(['message' => __('Saved Successfully'), 'type' => 'success']);
    }

    public function update(BrandRequest $request, Company $brand): JsonResponse
    {
        if (Helper::appIsDemo()) {
            return response()->json(__('This feature is disabled in Demo version.'), 419);
        }

        $this->service->update($request, $brand);

        return response()->json(['message' => __('Updated Successfully'), 'type' => 'success']);
    }

    public function getProducts(Company $brand): JsonResponse
    {
        return response()->json($brand->products);
    }
}
