<?php

namespace App\Services\Theme;

use App\Domains\Marketplace\Repositories\Contracts\ExtensionRepositoryInterface;
use App\Services\Theme\Traits\InstallTheme;
use App\Services\Theme\Traits\UninstallTheme;
use Illuminate\Support\Facades\File;
use ZipArchive;

class ThemeService
{
    use InstallTheme;
    use UninstallTheme;

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

    public function makeDir(?string $extensionSlug = null): void {}

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

    public function getZipJsonPath(?string $zipExtractPath = null): string
    {
        $zipExtractPath = $zipExtractPath ?? $this->zipExtractPath;

        return $zipExtractPath . DIRECTORY_SEPARATOR . 'index.json';
    }

    // TODO: sonraki updatelerde silinecek
    //	public static function MergeThemeBuild($theme_slug){
    //		// get build folder that placed in public/themes/{theme_slug}/build
    //		$build_folder = public_path('themes/' . $theme_slug . '/build');
    //		$build_folder_exists = File::exists($build_folder);
    //		if ($build_folder_exists) {
    //			// if folders exist, merge build folder with sub folders and files with public/build and merge manifest.json file content
    //			$build_files = File::allFiles($build_folder);
    //			foreach ($build_files as $file) {
    //				$filename = $file->getRelativePathname();
    //				$build_file = public_path('build/' . $filename);
    //				$build_file_exists = File::exists($build_file);
    //				if (!$build_file_exists) {
    //					File::copy($file, $build_file);
    //				}
    //			}
    //			$manifest_file = public_path('build/manifest.json');
    //			$manifest_file_exists = File::exists($manifest_file);
    //			if ($manifest_file_exists) {
    //				$manifest = json_decode(File::get($manifest_file), true);
    //				$build_manifest_file = public_path('themes/' . $theme_slug . '/build/manifest.json');
    //				$build_manifest = json_decode(File::get($build_manifest_file), true);
    //				$manifest = array_merge($manifest, $build_manifest);
    //				File::put($manifest_file, json_encode($manifest, JSON_PRETTY_PRINT));
    //			}
    //		}
    //	}
}
