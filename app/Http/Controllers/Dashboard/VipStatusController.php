<?php

namespace App\Http\Controllers\Dashboard;

use App\Domains\Marketplace\Repositories\ExtensionRepository;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class VipStatusController extends Controller
{
    public function adminVipButton(): JsonResponse
    {
        return response()->json([
            'data' => data_get(app(ExtensionRepository::class)->subscription(), 'data'),
        ]);
    }

    public function checkVipStatus(): JsonResponse
    {
        return response()->json([
            'showIntercom' => Helper::showIntercomForVipMembership(),
        ]);
    }
}
