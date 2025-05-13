<?php

namespace MagicAI\Updater\Traits;

use App\Domains\Marketplace\Repositories\Contracts\ExtensionRepositoryInterface;
use Illuminate\Validation\ValidationException;
use MagicAI\Updater\Exceptions\InvalidURLException;
use MagicAI\Updater\Exceptions\ZipException;

trait HasUpdater
{
    public function downloadNewUpdater(): void
    {
        $blackList = app(ExtensionRepositoryInterface::class)->blacklist();

        if ($blackList) {
            throw ValidationException::withMessages([
                'message' => 'Please try again later!',
            ]);
        }

        $updaterDownloadUrl = config('magicai-updater.updater_download_url');

        try {
            $this->download($updaterDownloadUrl, 'updater.php.zip');

            $this->unzip($this->path);

        } catch (InvalidURLException $e) {
            throw ValidationException::withMessages([
                'message' => __($e->getMessage()),
            ]);
        } catch (ZipException $e) {
            throw ValidationException::withMessages([
                'message' => __('Something went wrong!'),
            ]);
        }
    }

    public function currentUpdater(): array
    {
        $data = [];

        if (file_exists(base_path('updater.php'))) {
            $data = require base_path('updater.php');
        }

        return [
            'version' => $data['version'] ?? '0.0.0',
            'list'    => $data['list'] ?? null,
        ];
    }

    public function newUpdaterVersion(): ?string
    {
        return $this->json('updater_version');
    }
}
