<?php

namespace App\Services\DeFi;

use App\Http\Controllers\DeFi\DeFiContractAnalysesController;
use App\Http\Controllers\DeFi\DeFiMarketDataController;
use App\Http\Controllers\DeFi\DeFiMarketDataShowController;
use App\Http\Controllers\DeFi\DeFiMyPortfolioController;
use App\Http\Controllers\DeFi\DeFiNewsController;
use App\Http\Controllers\DeFi\DeFiSettingController;
use App\Http\Controllers\DeFi\DeFiSolutionController;
use App\Http\Controllers\DeFi\DeFiSynapseBridgeController;
use Illuminate\Support\Facades\Route;

class DeFi
{
    public static function routes(): \Illuminate\Routing\Router
    {
        return Route::group([
            'as'     => 'de-fi.',
            'prefix' => 'de-fi',
        ], function () {
            Route::get('', DeFiSolutionController::class)->name('index');
            Route::get('market-data', DeFiMarketDataController::class)->name('market-data');
            Route::get('market-data/{slug}', DeFiMarketDataShowController::class)->name('market-data.single');
            Route::get('news', DeFiNewsController::class)->name('news');
            Route::get('news/{slug}', [DeFiNewsController::class, 'show'])->name('news.single');
            Route::get('contract-analyses', DeFiContractAnalysesController::class)->name('contract-analyses');
            Route::get('setting', DeFiSettingController::class)->name('setting');
            Route::post('setting', [DeFiSettingController::class, 'update']);
            Route::get('my-portfolio', DeFiMyPortfolioController::class)->name('my-portfolio');
            Route::get('synapse-bridge', DeFiSynapseBridgeController::class)->name('synapse-bridge');
        });
    }
}
