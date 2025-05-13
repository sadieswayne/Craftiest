<?php

namespace MagicAI\Updater;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use MagicAI\Updater\Commands\UpdaterCommand;
use MagicAI\Updater\Controllers\UpdaterController;
use MagicAI\Updater\View\Components\Button;
use MagicAI\Updater\View\Components\Li;
use MagicAI\Updater\View\Components\Permission;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class UpdaterServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('magicai-updater')
            ->hasConfigFile()
            ->hasViews()
            ->hasViewComponents('updater', Permission::class, Li::class, Button::class)
            ->hasCommand(UpdaterCommand::class);
    }

    public function packageRegistered(): void
    {
        Route::prefix('updater')
            ->as('updater.')
            ->middleware(['web', 'auth', 'admin'])
            ->group(function (Router $router) {
                $router->get('', [UpdaterController::class, 'index'])->name('index');
                $router->get('check', [UpdaterController::class, 'check'])->name('check');
                $router->post('update', [UpdaterController::class, 'update'])->name('update');
                $router->any('backup', [UpdaterController::class, 'backup'])->name('backup');
                $router->any('upgrade', [UpdaterController::class, 'upgrade'])->name('upgrade');
                $router->get('for-panel', [UpdaterController::class, 'forPanel'])->name('for.panel');
                $router->get('version-check', [UpdaterController::class, 'versionCheck'])->name('version-check');
            });
    }
}
