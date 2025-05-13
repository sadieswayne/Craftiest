<?php

namespace App\Domains\Marketplace\Services;

use App\Domains\Marketplace\MarketplaceServiceProvider;
use App\Domains\Marketplace\Repositories\Contracts\ExtensionRepositoryInterface;
use App\Services\Extension\ExtensionService;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ExtensionUninstallService
{
    public function __construct(
        public ExtensionRepositoryInterface $repository
    ) {}

    public function uninstall(string $slug): array
    {
        $extension = $this->repository->findBySlugInDb($slug);

        $responseExtension = $this->repository->find($extension->getAttribute('slug'));

        $extensionFolderName = $responseExtension['extension_folder'];

        if (empty($extensionFolderName)) {
            return ['status'  => false, 'message' => 'Failed to find extension folder'];
        }

        if (Storage::disk('extension')->exists($extensionFolderName)) {

            if (File::exists(resource_path('extensions/' . $slug . '/index.json'))) {
                try {
                    app(ExtensionService::class)->uninstall(
                        extensionSlug: $slug, newVersion: false
                    );
                } catch (Exception $exception) {
                }
            }

            MarketplaceServiceProvider::uninstallExtension($slug);

            Storage::disk('extension')->deleteDirectory($extensionFolderName);

            Artisan::call('cache:clear');

            $extension
                ->update([
                    'installed' => 0,
                    'version'   => data_get($responseExtension, 'version'),
                ]);

            return [
                'status'  => true,
                'message' => 'Extension uninstalled successfully',
            ];
        }

        if (File::exists(resource_path('extensions/' . $slug . '/index.json'))) {
            try {
                app(ExtensionService::class)->uninstall(
                    extensionSlug: $slug, newVersion: false, uninstall: true
                );
            } catch (Exception $exception) {
            }
        }

        return [
            'status'  => false,
            'message' => 'Failed to uninstall extension',
        ];
    }
}
