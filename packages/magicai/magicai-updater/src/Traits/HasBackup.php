<?php

namespace MagicAI\Updater\Traits;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use ZipArchive;

trait HasBackup
{
    public string $backupFileNameCacheKey = 'backupFileName';

    public array $excepts = [
        '.git',
        'node_modules',
        '__MACOSX',
        '.idea',
        '.github',
        'storage/logs',
        'storage/framework/cache',
        'storage/framework/sessions',
        'storage/framework/views',
        'storage/framework/testing',
        'storage/app/backups',
        'vendor/magicai/magicai-updater/vendor',
        'public/uploads',
        'public/upload',
    ];

    public function backup(): bool
    {
        $this->configurePhp();

        Artisan::call('optimize:clear');

        $fileName = $this->backupFileName();

        $zipName = $this->backupFilePath($fileName);

        $zip = new ZipArchive;

        try {
            if ($zip->open($zipName, ZipArchive::CREATE) === true) {

                $this->addFolderToZip(base_path(), $zip);

                $zip->close();
            }
        } catch (Exception $e) {
            throw ValidationException::withMessages([
                'message' => __('Something went wrong!'),
            ]);
        }

        return $fileName;
    }

    public function exceptFolder(string $folder): bool
    {
        $exceptArray = array_map(static function ($item) {
            return base_path($item);
        }, $this->excepts);

        if (in_array($folder, $exceptArray, true)) {
            return true;
        }

        return false;
    }

    private function exceptFile(string $file): bool
    {
        if ($file === '.' || $file === '..') {
            return true;
        }

        // except .zip files
        if (pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
            return true;
        }

        return false;
    }

    private function addFolderToZip($folder, ZipArchive $zip, $parentFolder = ''): void
    {
        if ($this->exceptFolder($folder)) {
            return;
        }

        $files = scandir($folder);

        foreach ($files as $file) {
            if ($this->exceptFile($file)) {
                continue;
            }

            $filePath = $folder . DIRECTORY_SEPARATOR . $file;

            $relativePath = $parentFolder ? $parentFolder . DIRECTORY_SEPARATOR . $file : $file;

            if (is_dir($filePath)) {
                $zip->addEmptyDir($relativePath);

                $this->addFolderToZip($filePath, $zip, $relativePath);
            } else {
                if (is_file($filePath)) {
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }
    }

    private function backupFilePath(string $fileName): string
    {
        return base_path($fileName);
    }

    private function backupFileName(): string
    {
        $fileName = 'backup-' . date('Y-m-d_H-i') . '.zip';

        Cache::remember($this->backupFileNameCacheKey, now()->addMinutes(10), function () use ($fileName) {
            return $fileName;
        });

        return $fileName;
    }

    public function backupFileNameGetFromCache()
    {
        return Cache::get($this->backupFileNameCacheKey);
    }

    private function configurePhp(): void
    {
        // unlimited max execution time
        set_time_limit(0);

        // increase memory_limit to 1GB
        ini_set('memory_limit', '-1');

        // increase max_execution_time to 1 hour
        ini_set('max_execution_time', 3600);
    }
}
