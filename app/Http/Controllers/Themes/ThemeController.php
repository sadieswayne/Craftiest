<?php

namespace App\Http\Controllers\Themes;

use App\Domains\Marketplace\Repositories\Contracts\ExtensionRepositoryInterface;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Models\Extension;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function __construct(
        public ExtensionRepositoryInterface $extensionRepository
    ) {}

    public function index()
    {
        $items = $this->extensionRepository->themes();

        // sort the result desc
        $extensions = Extension::query()
            ->where('is_theme', true)->orderBy('id', 'desc')->get();

        $paymentStatus = request('payment_status');

        return view('panel.admin.themes.index', compact('extensions', 'items', 'paymentStatus'));
    }

    public function buyTheme($slug)
    {
        $item = $this->extensionRepository->find($slug);

        return view('panel.admin.themes.buy', compact('item'));
    }

    public function themeActivate(Request $request, string $token)
    {
        $data = Helper::decodePaymentToken($token);

        $item = $this->extensionRepository->find($data['slug']);

        return view('panel.admin.themes.activate', [
            'item'    => $item,
            'token'   => $token,
            'success' => $request->get('redirect_status') == 'succeeded',
        ]);
    }
}
