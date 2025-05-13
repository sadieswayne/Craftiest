<?php

namespace MagicAI\Updater\Controllers;

use App\Helpers\Classes\InstallationHelper;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use MagicAI\Updater\Facades\Updater;

class UpdaterController
{
    public function index(Request $request): View
    {
        return view('magicai-updater::index', [
            'permission' => true,
            'data'       => Updater::checker(),
            'user'       => $request->user(),
        ]);
    }

    public function check(): array
    {
        return Updater::checker();
    }

    public function update(Request $request): RedirectResponse
    {
        Updater::downloadNewUpdater();

        return back()->with([
            'message' => trans('The updater has been successfully downloaded.'),
            'type'    => 'success',
        ]);
    }

    public function backup(Request $request)
    {
        $data = Updater::backupView();

        if ($data['updated']) {
            return redirect()->route('updater.index')->with([
                'message' => 'MagicAI is already up to date.',
                'type'    => 'success',
            ]);
        }

        if ($request->isMethod('post')) {
            Updater::backup();
        }

        return view('magicai-updater::index', [
            'permission' => true,
            'data'       => Updater::backupView(),
            'fileName'   => Updater::backupFileNameGetFromCache(),
        ]);
    }

    public function upgrade(Request $request)
    {
        $backupFileName = Updater::backupFileNameGetFromCache();

        if (file_exists(base_path($backupFileName))) {

            Updater::updateNewVersion($backupFileName);

            return response([
                'message' => 'The backup file could not be found. Please try again.',
                'type'    => 'success',
            ], 200);
        }

        return response([
            'message' => 'The backup file could not be found. Please try again.',
            'type'    => 'success',
        ], 422);
    }

    public function versionCheck(): JsonResponse
    {
        $versionCheck = Updater::versionCheck();

        if ($versionCheck) {
            try {

                Artisan::call('migrate', ['--force' => true]);

                InstallationHelper::runInstallation();

                Artisan::call('optimize:clear');
            } catch (Exception $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'updated' => $versionCheck,
                ]);
            }
        }

        return response()->json([
            'message' => $versionCheck ? 'MagicAI is already up to date.' : 'MagicAI don\'t updated.',
            'updated' => $versionCheck,
        ]);
    }

    public function forPanel(): JsonResponse
    {
        return response()->json(Updater::forPanel());
    }
}
