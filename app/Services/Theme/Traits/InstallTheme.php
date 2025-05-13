<?php

namespace App\Services\Theme\Traits;

use App\Helpers\Classes\Helper;
use App\Models\Extension;
use Exception;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait InstallTheme
{
    public function install(string $extensionSlug): bool|array
    {
        if ($extensionSlug == 'default') {
            setting([
                'front_theme' => 'default',
                'dash_theme'  => 'default',
            ])->save();

            Artisan::call('optimize:clear');

            return [
                'success' => true,
                'status'  => true,
                'message' => trans('Theme installed successfully'),
            ];
        }

        $dbExtension = Extension::query()->where('slug', $extensionSlug)
            ->where('is_theme', true)
            ->firstOrFail();

        $responseExtension = $this->extensionRepository->find($dbExtension->getAttribute('slug'));

        $version = data_get($responseExtension, 'version');

        $response = $this->extensionRepository->install(
            $dbExtension->getAttribute('slug'),
            $version
        );

        if ($response->failed()) {
            return [
                'status'  => false,
                'message' => trans('Failed to download the theme file'),
            ];
        }

        $zipContent = $response->body();

        Storage::disk('local')->put('file.zip', $zipContent);

        $checkZip = $this->zipArchive->open(
            Storage::disk('local')->path('file.zip')
        );

        if ($checkZip) {

            $this->zipExtractPath = storage_path('app/zip-extract');

            $this->zipArchive->extractTo($this->zipExtractPath);

            $this->zipArchive->close();

            Storage::disk('local')->delete('file.zip');

            try {
                // index json
                $this->getIndexJson();

                if (empty($this->indexJsonArray)) {
                    return [
                        'status'  => false,
                        'message' => trans('index.json not found'),
                    ];
                }

                $theme = data_get($this->indexJsonArray, 'slug');

                if (! $theme) {
                    return [
                        'status'  => false,
                        'message' => trans('index.json not found'),
                    ];
                }

                $files = Storage::disk('local')->allFiles('zip-extract');

                foreach ($files as $file) {

                    $replaceDirName = "/$theme/";

                    if (Str::contains($file, 'zip-extract/public/assets')) {
                        $this->copyThemeFile($file, $replaceDirName, 'themes');
                    } elseif (Str::contains($file, 'zip-extract/public/build')) {
                        $this->copyThemeFile($file, '', 'build');
                    } else {
                        $this->copyThemeFile($file, $replaceDirName);
                    }
                }

                // delete zip extract dir
                (new Filesystem)->deleteDirectory($this->zipExtractPath);

                Extension::query()->where('slug', $extensionSlug)
                    ->update([
                        'installed' => 1,
                        'version'   => $version,
                    ]);

                $item = $this->extensionRepository->find($extensionSlug);

                $folderName = data_get($this->indexJsonArray, 'slug');

                if ($item['theme_type'] === 'Frontend') {
                    setting(['front_theme' => $folderName])->save();
                } elseif ($item['theme_type'] === 'Dashboard') {
                    setting(['dash_theme' => $folderName])->save();
                } else {
                    setting(['front_theme' => $folderName, 'dash_theme' => $folderName])->save();
                }

                Artisan::call('optimize:clear');

                return [
                    'success' => true,
                    'status'  => true,
                    'message' => trans('Theme installed successfully'),
                ];

            } catch (Exception $e) {
                return [
                    'status'  => false,
                    'message' => $e->getMessage(),
                ];
            }
        }
    }

    public function copyThemeFile(string $path = '', string $replace = '', string $disk = 'views'): void
    {
        $newPath = str_replace(['zip-extract/theme/', 'zip-extract/public/', 'zip-extract/'], $replace, $path);

        if ($disk == 'build') {
            $newPath = str_replace('build', '', $newPath);

            //            dd($newPath);
        }

        // For other files, simply add them
        $content = Storage::disk('local')->get($path);

        Storage::disk($disk)->put($newPath, $content);
    }

    /**
     * Check license function
     */
    public function checkLicense(?string $licenseKey = null): null|bool|string
    {
        $licenseKey = $licenseKey ?? Helper::settingTwo('liquid_license_domain_key');

        $response = Http::get('https://portal.liquid-themes.com/api/license/' . DIRECTORY_SEPARATOR . $licenseKey);

        if ($response->failed()) {
            return false;
        }

        return $response->json('owner.email');
    }
}
