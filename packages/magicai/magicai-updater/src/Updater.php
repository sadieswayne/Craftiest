<?php

namespace MagicAI\Updater;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use MagicAI\Updater\Traits\HasBackup;
use MagicAI\Updater\Traits\HasDownloader;
use MagicAI\Updater\Traits\HasUpdater;
use MagicAI\Updater\Traits\HasVersionUpdate;
use MagicAI\Updater\Traits\HasZipper;

class Updater
{
    use HasBackup;
    use HasDownloader;
    use HasUpdater;
    use HasVersionUpdate;
    use HasZipper;

    public function versionCheck(): bool|int
    {
        $magicAIVersion = $this->json('version');

        $currentMagicAIVersion = $this->currentMagicAIVersion();

        return version_compare($magicAIVersion, $currentMagicAIVersion, '=');
    }

    public function backupView(): array
    {
        $magicAIVersion = $this->json('version');

        $currentMagicAIVersion = $this->currentMagicAIVersion();

        if (version_compare($magicAIVersion, $currentMagicAIVersion, '=')) {
            return [
                'updated' => true,
            ];
        }

        return [
            'updated' => false,
            'title'   => trans('MagicAI installed successfully'),
            'version' => $this->json('version'),
            'view'    => 'magicai-updater::particles.backup',
            'step'    => 4,
        ];
    }

    public function checker(): array
    {
        $updaterVersion = $this->newUpdaterVersion();

        $currentUpdater = $this->currentUpdater();

        $magicAIVersion = $this->json('version');

        $currentMagicAIVersion = $this->currentMagicAIVersion();

        if (version_compare($magicAIVersion, $currentMagicAIVersion, '=')) {
            return [
                'title'   => trans('MagicAI installed successfully'),
                'version' => $this->json('version'),
                'view'    => 'magicai-updater::particles.updated',
            ];
        }

        if ($updaterVersion && version_compare($updaterVersion, $currentUpdater['version'], '=')) {
            return [
                'title'           => trans('MagicAI is ready to update'),
                'updater'         => $this->currentUpdater(),
                'version'         => $this->json('version'),
                'updater_version' => $this->json('updater_version'),
                'view'            => 'magicai-updater::particles.update',
                'step'            => 2,
            ];
        }

        return [
            'title'           => trans('MagicAI is ready to download check for updates'),
            'version'         => $this->json('version'),
            'updater_version' => $this->json('updater_version'),
            'view'            => 'magicai-updater::particles.updater',
            'step'            => 1,
        ];
    }

    public function forPanel(): array
    {
        $lastVersion = $this->versionRequest()->json() ?: [];

        if (version_compare($lastVersion['version'], $this->currentMagicAIVersion(), '>')) {
            $lastVersion['update'] = 'yes'; // Trigger the new version available.
            $lastVersion['version_format'] = format_double($lastVersion['version']);
        }

        return $lastVersion;
    }

    public function currentMagicAIVersion(): false|string
    {
        return trim(File::get(base_path('version.txt')));
    }

    public function json(string $key): null|string|array
    {
        return $this->versionRequest()->json($key);
    }

    public function versionRequest(): Response
    {
        return once(static function () {
            return Http::get(config('magicai-updater.version_url'));
        });
    }
}
