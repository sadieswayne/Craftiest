<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
| index     GET
| create    GET
| store     POST
| show      GET
| edit      GET
| update    PUT/PATCH
| destroy   DELETE
|
*/

Route::prefix('auth')
    ->group(function () {
        Route::post('register', 'App\Http\Controllers\Api\AuthController@register');
        Route::post('forgot-password', 'App\Http\Controllers\Api\AuthController@sendPasswordResetMail');
        Route::get('email/verify', 'App\Http\Controllers\Api\AuthController@emailConfirmationMail');
        Route::post('email/verify/resend', 'App\Http\Controllers\Api\AuthController@resend')->middleware('throttle:6,1');
        Route::get('social-login', 'App\Http\Controllers\Api\AuthController@getSupportedLoginMethods');
        Route::get('logo', 'App\Http\Controllers\Api\AppController@getLogo');
        Route::post('google-login', 'App\Http\Controllers\Api\SocialLoginController@google');
        Route::post('apple-login', 'App\Http\Controllers\Api\SocialLoginController@apple');
    });

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('logout', 'App\Http\Controllers\Api\AuthController@logout');

        Route::prefix('profile')->group(function () {
            Route::get('/', 'App\Http\Controllers\Api\UserController@index');
            Route::patch('/', 'App\Http\Controllers\Api\UserController@update');
            Route::delete('/', 'App\Http\Controllers\Api\UserController@destroy');
        });
    });
    Route::prefix('app')->group(function () {
        Route::get('email-confirmation-setting', 'App\Http\Controllers\Api\AppController@getEmailConfirmationSetting'); // if emailconfirmation = true -> then mail required, elseif emailconfirmation = false -> then email confirmation not required
        Route::get('get-setting', 'App\Http\Controllers\Api\AppController@getSetting'); // returns all settings
        Route::get('usage-data', 'App\Http\Controllers\Api\AppController@getUsageData'); // returns usage data and plan details of current user.
        Route::get('currency/{id?}', 'App\Http\Controllers\Api\AppController@getCurrency'); // returns default currency defined in settings.
    });

    Route::prefix('general')->group(function () {
        Route::get('recent-documents', 'App\Http\Controllers\Api\AIChatController@getRecentDocuments');
        Route::get('favorite-openai', 'App\Http\Controllers\Api\AIChatController@openAIFavoritesList'); // favorite ai tool (ai_voiceover, ai_wizard, ai_writer(facebook_ads, youtube_video_title ..etc) ..etc)
        Route::post('search', 'App\Http\Controllers\Api\AIChatController@search');
    });

    Route::prefix('aichat')->group(function () {

        Route::post('/stream-test', 'App\Http\Controllers\Api\AIChatController@developerStreamTest');
        Route::post('/change-chat-title', 'App\Http\Controllers\Api\AIChatController@changeChatTitle');

        Route::prefix('chat-templates')->group(function () {
            Route::get('/{id?}', 'App\Http\Controllers\Api\ChatTemplatesController@index');
            Route::patch('/', 'App\Http\Controllers\Api\ChatTemplatesController@update'); // If `request->template_id` is 'undefined' or `null`, a new template will be created.
            Route::delete('/{id}', 'App\Http\Controllers\Api\ChatTemplatesController@destroy');
        });

        Route::prefix('chat')->group(function () {
            Route::get('/{conver_id}', 'App\Http\Controllers\Api\AIChatController@conversations'); // get conversation chat by conversation id
            Route::get('/{conver_id}/messages', 'App\Http\Controllers\Api\AIChatController@conversationChats'); // get conversation chat by conversation id
            Route::get('/{conver_id}/messages/{id}', 'App\Http\Controllers\Api\AIChatController@conversationChats'); // get conversation chat by conversation id
        });
        // must be out of chat to not to match with {conver_id}
        Route::match(['get', 'post'], '/chat-send', 'App\Http\Controllers\Api\AIChatController@chatOutput');
        Route::post('/new-chat', 'App\Http\Controllers\Api\AIChatController@startNewChat');

        Route::prefix('history')->group(function () { // returns all conversations of the template
            Route::get('/{cat_slug}', 'App\Http\Controllers\Api\AIChatController@openAIChat');
            Route::delete('/', 'App\Http\Controllers\Api\AIChatController@deleteChat');
            Route::patch('/', 'App\Http\Controllers\Api\AIChatController@renameChat');
        });
        // must be out of history to not to match with {cat_slug}
        Route::post('/search-history', 'App\Http\Controllers\Api\AIChatController@searchChatHistory');
        Route::get('/recent-chats', 'App\Http\Controllers\Api\AIChatController@recentChats');
        Route::post('/search-recent-chats', 'App\Http\Controllers\Api\AIChatController@searchRecentChats');
    });

    // hasToken middleware is removed because of redirection. Checks will be done via mobile ui and api endpoints.
    Route::prefix('aiwriter')->group(function () { // aiwriter generators
        Route::get('/generator/{slug}', 'App\Http\Controllers\Api\AIWriterController@openAIGeneratorApi'); // returns the openai writer info and related user generated docs
        Route::get('/generator/{slug}/workbook', 'App\Http\Controllers\Api\AIWriterController@openAIGeneratorWorkbookApi'); // returns the openai writer info

        Route::post('/generate', 'App\Http\Controllers\Api\AIController@buildOutput'); // generate output {AIController}
        Route::post('/generate-output', 'App\Http\Controllers\Api\AIWriterController@streamedTextOutput'); // Streamed Text Output
        Route::get('/generate/lazyload', 'App\Http\Controllers\Api\AIWriterController@lazyLoadImage');
        Route::post('/generate/save', 'App\Http\Controllers\Api\AIWriterController@lowGenerateSave');

        Route::get('/openai-list', 'App\Http\Controllers\Api\AIWriterController@getOpenAIWriterList'); // returns all openai list related to text generation with user check
        Route::get('/favorite-openai-list', 'App\Http\Controllers\Api\AIWriterController@favoriteOpenaiList'); // returns all favorited openais id list [1,2,3] related to current user
        Route::post('/favorite-openai-list-add', 'App\Http\Controllers\Api\AIWriterController@addToFavoriteOpenaiList'); // returns all favorited openais id list [1,2,3] related to current user
        Route::post('/favorite-openai-list-remove', 'App\Http\Controllers\Api\AIWriterController@removeFromFavoriteOpenaiList'); // returns all favorited openais id list [1,2,3] related to current user
    });

    Route::prefix('aiimage')->group(function () {
        Route::get('/versions', 'App\Http\Controllers\Api\AIImageController@versions'); // returns the model versions
        Route::get('/check-availability', 'App\Http\Controllers\Api\AIImageController@checkActiveGeneration'); // returns if there is an active generation
        Route::post('/generate-image', 'App\Http\Controllers\Api\AIImageController@generateImage'); // returns generated images list
        Route::get('/recent-images', 'App\Http\Controllers\Api\AIImageController@getRecentImages'); // returns the latest 10 images
    });

    Route::prefix('payment')->group(function () {
        Route::get('/', "App\Http\Controllers\Api\PaymentApiController@getCurrentPlan");
        Route::get('/check-revenue-cat', "App\Http\Controllers\Api\PaymentApiController@checkRevenueCat");

        Route::get('/plans/{plan_id?}', "App\Http\Controllers\Api\PaymentApiController@plans");
        Route::get('/orders/{order_id?}', "App\Http\Controllers\Api\PaymentApiController@orders");

        Route::prefix('/subscriptions')->group(function () {
            // / Subscriptions are started from mobile app. So, there is no need to create a subscription from api for RevenueCat.
            Route::post('/cancel-current', "App\Http\Controllers\Api\PaymentApiController@cancelActiveSubscription");
        });
    });

    Route::prefix('affiliates')->group(function () {
        Route::get('/', "App\Http\Controllers\Api\AffiliateApiController@affiliates");
        Route::get('/withdrawals', "App\Http\Controllers\Api\AffiliateApiController@withdrawals");
        Route::post('/request-withdrawal', "App\Http\Controllers\Api\AffiliateApiController@requestWithdrawal");
    });

    Route::prefix('support')->group(function () {
        Route::get('/', "App\Http\Controllers\Api\UserSupportApiController@supportRequests");
        Route::post('/', "App\Http\Controllers\Api\UserSupportApiController@newTicket");
        Route::get('/ticket/{ticket_id}', "App\Http\Controllers\Api\UserSupportApiController@ticket");
        Route::get('/ticket/{ticket_id}/last-message', "App\Http\Controllers\Api\UserSupportApiController@ticketLastMessage");
        Route::post('/send-message', "App\Http\Controllers\Api\UserSupportApiController@sendMessage");
        Route::get('/user/{ticket_id}', "App\Http\Controllers\Api\UserSupportApiController@ticketUser");
    });

    Route::prefix('documents')->group(function () {
        Route::get('/', "App\Http\Controllers\Api\DocumentsApiController@getDocs");
        Route::get('/doc/{id}', "App\Http\Controllers\Api\DocumentsApiController@getDoc");
        Route::post('/doc/{id}', "App\Http\Controllers\Api\DocumentsApiController@saveDoc");
        Route::delete('/doc/{id}', "App\Http\Controllers\Api\DocumentsApiController@deleteDoc");
        Route::get('/recent', "App\Http\Controllers\Api\DocumentsApiController@getRecentDocs");
        Route::get('/all-openai', "App\Http\Controllers\Api\DocumentsApiController@getOpenAIList"); // returns all openai generators without user check
        Route::get('/openai-filters', "App\Http\Controllers\Api\DocumentsApiController@getOpenAIFilters");
    });

    Route::prefix('extensions')->group(function () {
        Route::get('/', "App\Http\Controllers\Api\ExtensionController@extensionIndex");

        // / Extension Routes for API
        $files = glob(base_path('routes/extapiroutes/*.php'));
        for ($i = 0; $i < count($files); $i++) {
            include $files[$i];
        }

    });

    Route::prefix('brandvoice')->group(function () {
        Route::get('/', "App\Http\Controllers\Api\BrandController@index");
        Route::get('/{id}', "App\Http\Controllers\Api\BrandController@index");
        Route::post('/', "App\Http\Controllers\Api\BrandController@store");
        Route::delete('/{id}', "App\Http\Controllers\Api\BrandController@delete");
    });

    Route::prefix('aivoiceover')->group(function () {
        Route::post('/preview', "App\Http\Controllers\Api\TTSController@preview");
        Route::post('/generate', "App\Http\Controllers\Api\TTSController@generate");
    });

    Route::prefix('entity')->group(function () {
        Route::get('/list', 'App\Http\Controllers\Api\EntityController@getAllEntities'); // returns all entities with tokens
    });
});
