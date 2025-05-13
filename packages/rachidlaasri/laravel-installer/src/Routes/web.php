<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use RachidLaasri\LaravelInstaller\Middleware\ApplicationCheckLicense;

Route::group([
    'prefix'              => 'install',
    'as'                  => 'LaravelInstaller::',
    'namespace'           => 'RachidLaasri\LaravelInstaller\Controllers',
    'middleware'          => ['web', 'install'],
    'excluded_middleware' => ['appstatus', ApplicationCheckLicense::class],
], static function () {
    Route::get('/', [
        'as'   => 'welcome',
        'uses' => 'WelcomeController@welcome',
    ]);

    Route::get('environment', [
        'as'   => 'environment',
        'uses' => 'EnvironmentController@environmentMenu',
    ]);

    Route::get('environment/wizard', [
        'as'   => 'environmentWizard',
        'uses' => 'EnvironmentController@environmentWizard',
    ]);

    Route::post('environment/saveWizard', [
        'as'   => 'environmentSaveWizard',
        'uses' => 'EnvironmentController@saveWizard',
    ]);

    Route::get('environment/classic', [
        'as'   => 'environmentClassic',
        'uses' => 'EnvironmentController@environmentClassic',
    ]);

    Route::post('environment/saveClassic', [
        'as'   => 'environmentSaveClassic',
        'uses' => 'EnvironmentController@saveClassic',
    ]);

    Route::get('requirements', [
        'as'   => 'requirements',
        'uses' => 'RequirementsController@requirements',
    ]);

    Route::get('permissions', [
        'as'   => 'permissions',
        'uses' => 'PermissionsController@permissions',
    ]);

    Route::get('database', [
        'as'   => 'database',
        'uses' => 'DatabaseController@database',
    ]);

    Route::get('final', [
        'as'                  => 'final',
        'uses'                => 'FinalController@finish',
        'excluded_middleware' => ['install'],
    ]);
});

Route::group([
    'prefix'              => 'update', 'as' => 'LaravelUpdater::', 'namespace' => 'RachidLaasri\LaravelInstaller\Controllers',
    'middleware'          => 'web',
    'excluded_middleware' => ['appstatus', ApplicationCheckLicense::class],
], static function () {

    Route::group(['middleware' => 'update'], static function () {
        Route::get('/', [
            'as'   => 'welcome',
            'uses' => 'UpdateController@welcome',
        ]);

        Route::get('overview', [
            'as'   => 'overview',
            'uses' => 'UpdateController@overview',
        ]);

        Route::get('database', [
            'as'   => 'database',
            'uses' => 'UpdateController@database',
        ]);
    });

    // This needs to be out of the middleware because right after the migration has been
    // run, the middleware sends a 404.
    Route::get('final', [
        'as'   => 'final',
        'uses' => 'UpdateController@finish',
    ]);
});

Route::group([
    'as'                  => 'LaravelInstaller::',
    'namespace'           => 'RachidLaasri\LaravelInstaller\Controllers',
    'middleware'          => ['web'],
    'excluded_middleware' => ['appstatus', ApplicationCheckLicense::class],
], static function () {
    Route::get('license/{regenerate?}', [
        'as'   => 'license',
        'uses' => 'ApplicationStatusController@license',
    ]);

    Route::get('license/upgrade', [
        'as'   => 'license.upgrade',
        'uses' => 'ApplicationStatusController@upgrade',
    ]);

    Route::post('license', [
        'uses' => 'ApplicationStatusController@licenseCheck',
    ]);

    Route::get('activate', [
        'uses' => 'ApplicationStatusController@activate',
    ])->name('license.activate');

    Route::any(
        'api/webhook/license', 'ApplicationStatusController@webhook'
    )->name('license.webhook')
        ->middleware('api')
        ->withoutMiddleware('web');

    Route::get('subscription', [
        'as'   => 'subscription',
        'uses' => 'SubscriptionController@index',
    ]);

    Route::any(
        'api/webhook/subscription/{key}/extension/{slug}', 'SubscriptionController@webhook'
    )->name('subscription.webhook')
        ->middleware('api')
        ->withoutMiddleware('web');
});
