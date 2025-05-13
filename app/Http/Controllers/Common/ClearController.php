<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Throwable;

class ClearController extends Controller
{
    public function cacheClear()
    {
        try {
            Artisan::call('optimize:clear');

            return response()->json(['success' => true]);
        } catch (Throwable $th) {
            return response()->json(['success' => false]);
        }
    }

    public function clearLog(): JsonResponse
    {
        $logFile = storage_path('logs/laravel.log');

        if (file_exists($logFile)) {
            unlink($logFile);
        }

        return response()->json(['success' => true]);
    }
}
