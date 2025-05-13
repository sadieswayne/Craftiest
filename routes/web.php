<?php

declare(strict_types=1);

use App\Http\Controllers\BlogController;
use App\Http\Controllers\Common\CheckSubscriptionEndController;
use App\Http\Controllers\Common\ClearController;
use App\Http\Controllers\Common\DebugModeController;
use App\Http\Controllers\Common\LocaleController;
use App\Http\Controllers\Common\SitemapController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\InstallationController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Payment\PlanAndPricingController;
use App\Http\Controllers\PrivatePlanController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;
use RachidLaasri\LaravelInstaller\Middleware\ApplicationStatus;

Route::get('language/{lang}/change', LocaleController::class)->name('language.change');

Route::get('test', [TestController::class, 'test'])->name('test');

Route::view('test/chatbot', 'default.chatbot');
Route::get('default', static function () {
    return response()->noContent();
})->name('default');

Route::middleware('checkInstallation')
    ->group(static function () {
        Route::get('', IndexController::class)->name('index');
        Route::controller(PageController::class)
            ->group(static function () {
                Route::get('privacy-policy', 'pagePrivacy')->name('pagePrivacy');
                Route::get('terms', 'pageTerms')->name('pageTerms');
                Route::get('page/{slug}', 'pageContent')->name('pageContent');
            });

        Route::controller(BlogController::class)
            ->group(static function () {
                Route::get('blog', 'index')->name('blog.index');
                Route::get('blog/{slug}', 'post')->name('blog.post');
                Route::get('blog/tag/{slug}', 'tags')->name('blog.tags');
                Route::get('blog/category/{slug}', 'categories')->name('blog.categories');
                Route::get('blog/author/{slug}', 'author')->name('blog.author');
            });

        Route::get('credit-list-partial', [PlanAndPricingController::class, 'creditListPartial'])->name('credit-list-partial');
    });

Route::get('sitemap.xml', [SitemapController::class, 'index']);
Route::get('plan/private/subscription/{key}', [PrivatePlanController::class, 'index']);
Route::get('confirm/email/{email_confirmation_code}', [MailController::class, 'emailConfirmationMail']);

Route::controller(InstallationController::class)
    ->group(static function () {
        Route::get('upgrade-script', 'upgrade')->withoutMiddleware(ApplicationStatus::class)->name('upgrade-script');
        Route::get('update-manual/{pass?}', 'updateManual')->withoutMiddleware(ApplicationStatus::class)->name('update-manual');
        Route::get('cache-clear-menu', 'menuClearCache')->name('menuClearCache');
        Route::post('install-extension/{slug}', 'installExtension')->name('install-extension');
        Route::post('uninstall-extension/{slug}', 'uninstallExtension')->name('uninstall-extension');

    });

Route::get('clear-log', [ClearController::class, 'clearLog'])->name('clearLog');
Route::get('cache-clear', [ClearController::class, 'cacheClear'])->name('cache.clear');

Route::get('debug/{token}', DebugModeController::class)->name('debug');
Route::get('check-subscription-end', CheckSubscriptionEndController::class)->name('check-subscription-end');

if (file_exists(base_path('routes/custom_routes_web.php'))) {
    include base_path('routes/custom_routes_web.php');
}

require __DIR__ . '/auth.php';
require __DIR__ . '/panel.php';
require __DIR__ . '/webhooks.php';
