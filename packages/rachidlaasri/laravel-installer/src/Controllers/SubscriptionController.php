<?php

namespace RachidLaasri\LaravelInstaller\Controllers;

use App\Domains\Marketplace\Repositories\ExtensionRepository;
use App\Services\Extension\ExtensionService;
use App\Services\Theme\ThemeService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SubscriptionController extends Controller
{
    public function __construct(public ExtensionRepository $extensionRepository) {}

    public function index(): Factory|Application|View|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $paidItems = $this->extensionRepository->paidExtensions();

        $subscription = $this->extensionRepository->subscription();

        $payment = data_get($subscription, 'payment');

        $data = data_get($subscription, 'data');

        return view('vendor.installer.subscription', [
            'payment'        => $payment,
            'data'           => $data,
            'paidThemes'     => collect($paidItems)->where('is_theme', true),
            'paidExtensions' => collect($paidItems)->where('is_theme', false),
        ]);
    }

    public function webhook(Request $request, string $key, string $slug): JsonResponse
    {
        if ($key == $this->extensionRepository->domainKey()) {

            app(ExtensionService::class)->uninstall($slug);

            $themes = [
                'sleek', 'creative', 'classic', 'dark',
            ];

            if (in_array($slug, $themes)) {
                app(ThemeService::class)->install('default');
            }

            return response()
                ->json([
                    'status' => 'success',
                ]);
        }

        return response()->json(['status' => 'fail']);
    }
}
