<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\ResultStores\ResultStore;
use Throwable;

class HealthController extends Controller
{
    public function index()
    {
        $resultStore = App::make(ResultStore::class);
        $checkResults = $resultStore->latestResults();

        // call new status when visit the page
        Artisan::call(RunHealthChecksCommand::class);

        return view('panel.admin.health.index', [
            'lastRanAt'    => new Carbon($checkResults?->finishedAt),
            'checkResults' => $checkResults,
        ]);
    }

    public function logs()
    {
        return view('panel.admin.health.logs');
    }

    public function cacheClear()
    {
        try {
            Artisan::call('optimize:clear');

            return response()->json(['success' => true]);
        } catch (Throwable $th) {
            return response()->json(['success' => false]);
        }
    }
}
