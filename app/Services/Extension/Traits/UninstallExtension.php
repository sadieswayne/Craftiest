<?php

namespace App\Services\Extension\Traits;

use App\Domains\Marketplace\Services\ExtensionUninstallService;
use App\Models\Extension;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

trait UninstallExtension
{
    public function uninstall(string $extensionSlug, $newVersion = true, $uninstall = true): bool|array
    {
        $responseExtension = $this->extensionRepository->find($extensionSlug);

        if (! $responseExtension) {
            return [
                'status'  => false,
                'message' => trans('index.json not found'),
            ];
        }

        $extensionFolderName = $responseExtension['extension_folder'] ?? null;

        if ($extensionFolderName && $this->extensionRepository->appVersion() >= 7.3 && $newVersion) {
            return app(ExtensionUninstallService::class)
                ->uninstall($extensionSlug);
        }

        try {
            $this->extensionSlug = $extensionSlug;

            $this->zipExtractPath = resource_path('extensions' . DIRECTORY_SEPARATOR . $extensionSlug);

            $this->getIndexJson();

            if (empty($this->indexJsonArray)) {
                return [
                    'status'  => false,
                    'message' => trans('index.json not found'),
                ];
            }

            $this->deleteOldVersionFiles();

            $this->uninstallFilesDelete();

            $this->deleteRoute();

            if ($uninstall) {
                $this->runUninstallQuery();
            }

            $this->deleteControllers();

            $this->deleteResource();

            Artisan::call('optimize:clear');

            if ($uninstall) {
                Extension::query()->where('slug', $extensionSlug)
                    ->update([
                        'installed' => 0,
                    ]);
            }

            Artisan::call('cache:clear');

            if ($extensionSlug == 'maintenance') {
                cache()->forget('maintenance');
            }

            return [
                'success' => true,
                'status'  => true,
                'message' => trans('Extension uninstalled successfully'),
            ];
        } catch (Exception $e) {
            return [
                'status'  => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function uninstallFilesDelete(): void
    {
        $files = data_get($this->indexJsonArray, 'stubs', []);

        if (empty($files) && ! is_array($files)) {
            return;
        }

        foreach ($files as $file) {
            $destinationPath = base_path($file);
            if (File::exists($destinationPath)) {
                File::delete($destinationPath);
            }
        }
    }

    public function deleteRoute(): void
    {
        $route = data_get($this->indexJsonArray, 'route', null);

        if (empty($route)) {
            return;
        }

        $routePath = base_path('routes/extroutes/' . basename($route));

        if (File::exists($routePath)) {
            File::delete($routePath);
        }
    }

    public function deleteControllers(): void
    {
        $controllers = data_get($this->indexJsonArray, 'controllers', []);

        if (empty($controllers) && ! is_array($controllers)) {
            return;
        }

        foreach ($controllers as $controller) {
            $path = base_path($controller);
            if (File::exists($path)) {
                File::delete($path);
            }
        }
    }

    public function runUninstallQuery()
    {
        $data = data_get($this->indexJsonArray, 'migrations.uninstall');

        if (empty($data)) {
            return;
        }

        foreach ($data as $value) {
            $table = data_get($value, 'condition.table');

            $column = data_get($value, 'condition.column', null);

            $sqlPath = $this->zipExtractPath . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR . data_get($value, 'path');

            if (
                Schema::hasTable($table)
                && File::exists($sqlPath)
                && is_null($column)
            ) {
                $query = $this->installQuery(
                    $sqlPath
                );

                DB::unprepared($query);
            } elseif (
                Schema::hasTable($table)
                && File::exists($sqlPath)
                && $column
            ) {
                $query = $this->installQuery(
                    $sqlPath
                );

                $column = data_get($value, 'condition.column');

                if (Schema::hasColumn($table, $column)) {
                    DB::unprepared($query);
                }
            }
        }
    }

    public function deleteResource(): void
    {
        File::deleteDirectory(resource_path("extensions/$this->extensionSlug"));
    }

    public function uninstallQuery(?string $zipExtractPath = null): bool|string
    {
        $zipExtractPath = $zipExtractPath ?? $this->zipExtractPath;

        return file_get_contents($zipExtractPath . DIRECTORY_SEPARATOR . 'uninstall.sql');
    }
}
