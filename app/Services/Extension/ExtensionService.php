<?php

namespace App\Services\Extension;

use App\Domains\Marketplace\Repositories\Contracts\ExtensionRepositoryInterface;
use App\Services\Extension\Traits\InstallExtension;
use App\Services\Extension\Traits\UninstallExtension;
use Illuminate\Support\Facades\File;
use ZipArchive;

class ExtensionService
{
    use InstallExtension;
    use UninstallExtension;

    public string $zipExtractPath;

    public string $extensionSlug;

    public string $indexJson;

    public array $indexJsonArray;

    public function __construct(
        public ZipArchive $zipArchive,
        public ExtensionRepositoryInterface $extensionRepository
    ) {}

    public function deleteOldVersionFiles(): void
    {
        $data = data_get($this->indexJsonArray, 'delete_old_version_files');

        if (empty($data) && ! is_array($data)) {
            return;
        }

        foreach ($data as $file) {
            $destinationPath = base_path($file);

            if (File::exists($destinationPath)) {
                File::delete($destinationPath);
            }
        }
    }

    public function makeDir(?string $extensionSlug = null): void
    {
        $extensionSlug = $extensionSlug ?? $this->extensionSlug;

        // make resource dir for extension
        if (! File::isDirectory(resource_path("extensions/$extensionSlug/"))) {
            File::makeDirectory(resource_path("extensions/$extensionSlug/"), 0777, true);
        }

        // make resource dir for extension
        if (! File::isDirectory(resource_path("extensions/$extensionSlug/migrations/uninstall"))) {
            File::makeDirectory(resource_path("extensions/$extensionSlug/migrations/uninstall"), 0777, true);
        }

        // make routes dir for extension
        if (! File::isDirectory(base_path('routes/extroutes/'))) {
            File::makeDirectory(base_path('routes/extroutes/'), 0777, true);
        }

        // make header views dir for extension
        if (! File::isDirectory(resource_path('views/default/components/navbar/extnavbars'))) {
            File::makeDirectory(resource_path('views/default/components/navbar/extnavbars'), 0777, true);
        }
    }

    /**
     * Get index.json from extracted zip
     */
    public function getIndexJson(?string $zipExtractPath = null): bool|string
    {
        $zipExtractPath = $zipExtractPath ?? $this->zipExtractPath;

        $path = $this->getZipJsonPath($zipExtractPath);

        if (! File::exists($path)) {
            return false;
        }

        $this->indexJson = file_get_contents(
            $this->getZipJsonPath($zipExtractPath)
        );

        if ($this->indexJson) {
            $this->indexJsonArray = json_decode($this->indexJson, true);
        }

        return $this->indexJson;
    }

    /**
     * Extracted zip json path
     */
    public function getZipJsonPath(?string $zipExtractPath = null): string
    {
        $zipExtractPath = $zipExtractPath ?? $this->zipExtractPath;

        return $zipExtractPath . DIRECTORY_SEPARATOR . 'index.json';
    }
}
