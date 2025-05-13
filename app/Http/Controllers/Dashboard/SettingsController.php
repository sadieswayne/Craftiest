<?php

namespace App\Http\Controllers\Dashboard;

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Entity\Enums\EntityEnum;
use App\Helpers\Classes\ApiHelper;
use App\Helpers\Classes\Helper;
use App\Helpers\Classes\MarketplaceHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Settings\GeneralSettingsRequest;
use App\Models\Extension;
use App\Models\OpenAIGenerator;
use App\Models\PrivacyTerms;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\User;
use App\Services\Common\MenuService;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\View\View;
use OpenAI;
use Throwable;

class SettingsController extends Controller
{
    public function general()
    {
        return view('panel.admin.settings.general', [
            'chatSetting' => Extension::query()
                ->where('slug', 'chat-setting')
                ->where('installed', true)
                ->exists(),
        ]);
    }

    public function generalSave(GeneralSettingsRequest $request): JsonResponse
    {
        if (Helper::appIsNotDemo()) {
            $settings = Setting::getCache();
            $settings_two = SettingTwo::getCache();

            $metaTitleLocal = $request->metaTitleLocal;
            $metaDescLocal = $request->metaDescLocal;

            if ($metaTitleLocal === $settings_two->languages_default) {
                $settings->meta_title = $request->meta_title;
            } else {
                $meta_title = PrivacyTerms::where('type', 'meta_title')->where('lang', $metaTitleLocal)->first();
                if ($meta_title) {
                    $meta_title->content = $request->meta_title;
                    $meta_title->save();
                } else {
                    $new_meta_title = new PrivacyTerms;
                    $new_meta_title->type = 'meta_title';
                    $new_meta_title->lang = $metaTitleLocal;
                    $new_meta_title->content = $request->meta_title;
                    $new_meta_title->save();
                }
            }

            if ($metaDescLocal === $settings_two->languages_default) {
                $settings->meta_description = $request->meta_description;
            } else {
                $meta_description = PrivacyTerms::where('type', 'meta_desc')->where('lang', $metaDescLocal)->first();
                if ($meta_description) {
                    $meta_description->content = $request->meta_description;
                    $meta_description->save();
                } else {
                    $new_meta_description = new PrivacyTerms;
                    $new_meta_description->type = 'meta_desc';
                    $new_meta_description->lang = $metaDescLocal;
                    $new_meta_description->content = $request->meta_description;
                    $new_meta_description->save();
                }
            }

            $inputs = [];
            $fields = [
                'chat_setting_for_customer',
                'default_ai_engine',
                'default_aw_image_engine',
                'photo_studio',
            ];
            foreach ($fields as $field) {
                if ($request->has($field)) {
                    $inputs[$field] = $request->$field;
                }
            }
            if (! empty($inputs)) {
                setting($inputs)->save();
            }

            $settings->site_name = $request->site_name;
            $settings->site_url = $request->site_url;
            $settings->site_email = $request->site_email;
            $settings->default_country = $request->default_country;
            $settings->default_currency = $request->default_currency;
            $settings->register_active = $request->register_active;
            $settings->login_with_otp = $request->login_with_otp;
            $settings->tour_seen = $request->tour_seen;
            $settings->google_analytics_code = $request->google_analytics_code;
            $settings->meta_keywords = $request->meta_keywords;
            $settings->dashboard_code_before_head = $request->dashboard_code_before_head;
            $settings->dashboard_code_before_body = $request->dashboard_code_before_body;
            $settings->feature_ai_writer = $request->feature_ai_writer;
            $settings->feature_ai_rewriter = $request->feature_ai_rewriter;
            $settings->feature_ai_chat_image = $request->feature_ai_chat_image;
            $settings->feature_ai_image = $request->feature_ai_image;
            $settings_two->feature_ai_video = $request->feature_ai_video;
            $settings->feature_ai_chat = $request->feature_ai_chat;
            $settings->feature_ai_code = $request->feature_ai_code;
            $settings->feature_ai_speech_to_text = $request->feature_ai_speech_to_text;
            $settings->feature_ai_voiceover = $request->feature_ai_voiceover;
            $settings->feature_affilates = $request->feature_affilates;
            $settings->user_api_option = $request->user_api_option;
            $settings->feature_ai_article_wizard = $request->feature_ai_article_wizard;
            $settings->feature_ai_vision = $request->feature_ai_vision;
            $settings->feature_ai_pdf = $request->feature_ai_pdf;
            $settings->feature_ai_youtube = $request->feature_ai_youtube;
            $settings->feature_ai_rss = $request->feature_ai_rss;
            $settings->feature_ai_voice_clone = (bool) $request->feature_ai_voice_clone;
            $settings->team_functionality = $request->team_functionality;
            $settings->feature_ai_advanced_editor = $request->feature_ai_advanced_editor;
            $settings->login_without_confirmation = $request->login_without_confirmation;
            $settings->facebook_active = $request->facebook_active ?? 0;
            $settings->google_active = $request->google_active ?? 0;
            $settings->github_active = $request->github_active ?? 0;
            $settings->mobile_payment_active = $request->mobile_payment_active ?? 0;
            $settings->recaptcha_login = $request->recaptcha_login;
            $settings->recaptcha_register = $request->recaptcha_register;
            $settings->recaptcha_sitekey = $request->recaptcha_sitekey;
            $settings->recaptcha_secretkey = $request->recaptcha_secretkey;
            $settings->mrrobot_name = $request->mrrobot_name;
            $settings->mrrobot_search_words = $request->mrrobot_search_words;
            $settings->save();

            setting(
                [
                    'notification_active'             => $request->notification_active,
                    'pusher_app_id'                   => $request->pusher_app_id,
                    'pusher_app_key'                  => $request->pusher_app_key,
                    'pusher_app_secret'               => $request->pusher_app_secret,
                    'pusher_app_cluster'              => $request->pusher_app_cluster,
                    'user_prompt_library'             => $request->user_prompt_library,
                    'user_ai_image_prompt_library'    => $request->user_ai_image_prompt_library,
                    'ai_voice_isolator'               => $request->ai_voice_isolator,
                    'user_ai_writer_custom_templates' => $request->user_ai_writer_custom_templates,
                    'select_model_option'             => $request->select_model_option,
                    'freeCreditsUponRegistration'     => $request->entities,
                    'onetime_commission'              => $request->onetime_commission,
                ]
            )->save();

            $this->toggleOpenaiTemplateStatus($settings);

            $settings_two->daily_limit_enabled = $request->limit;
            $settings_two->allowed_images_count = $request->daily_limit_count;
            $settings_two->openai_default_stream_server = $request->openai_default_stream_server;
            $settings_two->daily_voice_limit_enabled = $request->voice_limit;
            $settings_two->allowed_voice_count = $request->daily_voice_limit_count;

            $settings_two->save();

            $logo_types = [
                'logo'                => '',
                'logo_dark'           => 'dark',
                'logo_sticky'         => 'sticky',
                'logo_dashboard'      => 'dashboard',
                'logo_dashboard_dark' => 'dashboard-dark',
                'logo_collapsed'      => 'collapsed',
                'logo_collapsed_dark' => 'collapsed-dark',
                // retina
                'logo_2x'                => '2x',
                'logo_dark_2x'           => 'dark-2x',
                'logo_sticky_2x'         => 'sticky-2x',
                'logo_dashboard_2x'      => 'dashboard-2x',
                'logo_dashboard_dark_2x' => 'dashboard-dark-2x',
                'logo_collapsed_2x'      => 'collapsed-2x',
                'logo_collapsed_dark_2x' => 'collapsed-dark-2x',
            ];

            foreach ($logo_types as $logo => $logo_prefix) {

                if ($request->hasFile($logo)) {
                    $path = 'upload/images/logo/';
                    $image = $request->file($logo);
                    $image_name = Str::random(4) . '-' . $logo_prefix . '-' . Str::slug($settings->site_name) . '-logo.' . $image->getClientOriginalExtension();

                    // Resim uzantı kontrolü
                    $imageTypes = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
                    if (! in_array(Str::lower($image->getClientOriginalExtension()), $imageTypes)) {
                        $data = [
                            'errors' => ['The file extension must be jpg, jpeg, png, webp or svg.'],
                        ];

                        return response()->json($data, 419);
                    }

                    $image->move($path, $image_name);

                    $settings->{$logo . '_path'} = $path . $image_name;
                    $settings->{$logo} = $image_name;
                    $settings->save();
                }

            }

            if ($request->hasFile('favicon')) {
                $path = 'upload/images/favicon/';
                $image = $request->file('favicon');
                $image_name = Str::random(4) . '-' . Str::slug($settings->site_name) . '-favicon.' . $image->getClientOriginalExtension();

                // Resim uzantı kontrolü
                $imageTypes = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
                if (! in_array(Str::lower($image->getClientOriginalExtension()), $imageTypes)) {
                    $data = [
                        'errors' => ['The file extension must be jpg, jpeg, png, webp or svg.'],
                    ];

                    return response()->json($data, 419);
                }

                $image->move($path, $image_name);

                $settings->favicon_path = $path . $image_name;
                $settings->favicon = $image_name;
                $settings->save();
            }

            app(MenuService::class)->regenerate();
        }

        return response()->json([], 200);
    }

    public function toggleOpenaiTemplateStatus($settings)
    {
        $templates = [
            'ai_article_wizard_generator' => $settings->feature_ai_article_wizard,
            'ai_writer'                   => $settings->feature_ai_writer,
            'ai_rewriter'                 => $settings->feature_ai_rewriter,
            'ai_chat_image'               => $settings->feature_ai_chat_image,
            'ai_image_generator'          => $settings->feature_ai_image,
            'ai_code_generator'           => $settings->feature_ai_code,
            'ai_speech_to_text'           => $settings->feature_ai_speech_to_text,
            'ai_voiceover'                => $settings->feature_ai_voiceover,
            'ai_vision'                   => $settings->feature_ai_vision,
            'ai_pdf'                      => $settings->feature_ai_pdf,
            'ai_youtube'                  => $settings->feature_ai_youtube,
            'ai_rss'                      => $settings->feature_ai_rss,
        ];

        foreach ($templates as $key => $status) {
            OpenAIGenerator::query()->where('slug', $key)->update(['active' => $status]);
        }
    }

    public function anthropic(Request $request)
    {
        if (Helper::appIsDemo()) {
            return to_route('dashboard.user.index')->with([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        return view('panel.admin.settings.anthropic');
    }

    public function deepseek(Request $request)
    {
        if (Helper::appIsDemo()) {
            return to_route('dashboard.user.index')->with([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        return view('panel.admin.settings.deepseek');
    }

    public function deepseekSave(Request $request)
    {
        $data = $request->validate([
            'deepseek_api_secret'         => 'required',
            'deepseek_default_model'      => 'required|string',
            'deepseek_max_output_length'  => 'required|string',
        ]);

        $data['deepseek_api_secret'] = implode(',', $data['deepseek_api_secret']);

        setting($data)->save();

        return redirect()->back()->with([
            'type'    => 'success',
            'message' => __('setting saved successfully.'),
        ]);
    }

    public function deepseekTest()
    {
        if (Helper::appIsDemo()) {
            return to_route('dashboard.user.index')->with([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        $token = ApiHelper::setDeepseekKey();

        $url = 'https://api.deepseek.com/models';

        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Authorization' => "Bearer $token",
        ])->get($url);

        // Yanıtı Kontrol Etme
        if ($response->successful()) {
            echo ' <br>' . $token . ' - SUCCESS <br>';
        } else {
            echo $response->json('error.message') . ' - ' . $token . ' -FAILED <br>';
        }
    }

    public function anthropicTest()
    {
        if (Helper::appIsDemo()) {
            return to_route('dashboard.user.index')->with([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        $token = ApiHelper::setAnthropicKey();

        $request = Http::withHeaders([
            'x-api-key'         => $token,
            'Content-Type'      => 'application/json',
            'Accept'            => 'application/json',
            'anthropic-version' => '2023-06-01',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model'      => setting('anthropic_default_model'),
            'max_tokens' => (int) setting('anthropic_max_output_length'),
            'messages'   => [
                [
                    'role'    => 'user',
                    'content' => 'Hello, how are you?',
                ],
            ],
        ]);

        if ($request->ok()) {
            echo ' <br>' . $token . ' - SUCCESS <br>';
        } else {
            echo $request->json('error.message') . ' - ' . $token . ' -FAILED <br>';
        }
    }

    public function anthropicSave(Request $request)
    {
        $data = $request->validate([
            'anthropic_api_secret'        => 'required|string',
            'anthropic_default_model'     => 'required|string',
            'anthropic_max_input_length'  => 'required|string',
            'anthropic_max_output_length' => 'required|string',
            'anthropic_bedrock_model'     => 'string',
        ]);

        setting($data)->save();

        return response()->json([], 200);
    }

    public function gemini(Request $request)
    {
        if (Helper::appIsDemo()) {
            return to_route('dashboard.user.index')->with([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        return view('panel.admin.settings.gemini');
    }

    public function geminiTest()
    {
        if (Helper::appIsDemo()) {
            return to_route('dashboard.user.index')->with([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        $newhistory = [
            [
                'role'  => 'user',
                'parts' => [
                    [
                        'text' => 'who are u.',
                    ],
                ],
            ],
        ];

        $randomToken = ApiHelper::setGeminiKey();
        $client = app(\App\Domains\Engine\Services\GeminiService::class);
        $response = $client
            ->setHistory($newhistory)
            ->generateContent();
        if ($response->ok()) {
            echo ' <br>' . $randomToken . ' - SUCCESS <br>';
        } else {
            echo $response->json('error.message') . ' -FAILED <br>';
        }
    }

    public function geminiSave(Request $request)
    {
        $data = $request->validate([
            'gemini_api_secret'        => 'required|string',
            'gemini_default_model'     => 'required|string',
            'gemini_max_input_length'  => 'required|string',
            'gemini_max_output_length' => 'required|string',
        ]);
        setting($data)->save();

        return response()->json([], 200);
    }

    public function xAI()
    {
        if (Helper::appIsDemo()) {
            return to_route('dashboard.user.index')->with([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        return view('panel.admin.settings.x-ai');
    }

    public function openai()
    {
        if (Helper::appIsDemo()) {
            return to_route('dashboard.user.index')->with([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        return view('panel.admin.settings.openai');
    }

    public function stablediffusion()
    {
        return view('panel.admin.settings.stablediffusion');
    }

    public function unsplashapi(Request $request)
    {
        return view('panel.admin.settings.unsplashapi');
    }

    public function unsplashapiTest()
    {
        $client = new Client;
        $settings = SettingTwo::getCache();
        if ($settings->unsplash_api_key == '') {
            echo 'You must provide ' . EngineEnum::UNSPLASH->label() . ' API Key.';

            return;
        }

        $apiKey = $settings->unsplash_api_key;

        $client = new Client;

        try {
            $response = $client->get("https://api.unsplash.com/search/photos?query=Google&count=1&client_id=$apiKey");
            echo ' <br>' . $apiKey . ' - SUCCESS <br>';
        } catch (Exception $e) {
            echo $e->getMessage() . ' - ' . $apiKey . ' -FAILED <br>';
        }
    }

    public function unsplashapiSave(Request $request)
    {
        $settings = SettingTwo::getCache();
        if (Helper::appIsNotDemo()) {
            $settings->unsplash_api_key = $request->unsplash_api_key;
            $settings->save();
        }

        return response()->json([], 200);
    }

    public function pexelsapi(Request $request)
    {
        return view('panel.admin.settings.pexels');
    }

    public function pexelsapiTest()
    {
        $client = new Client;
        $api = setting('pexels_api_key');
        if ($api == '') {
            echo 'You must provide Pexels API Key.';

            return;
        }

        $apiKey = $api;

        $client = new Client;

        try {
            $response = $client->get('https://api.pexels.com/v1/search?query=Google&per_page=1', [
                'headers' => [
                    'Authorization' => $apiKey,
                ],
            ]);
            echo ' <br>' . $apiKey . ' - SUCCESS <br>';
        } catch (Exception $e) {
            echo $e->getMessage() . ' - ' . $apiKey . ' -FAILED <br>';
        }
    }

    public function pexelsapiSave(Request $request)
    {
        if (Helper::appIsNotDemo()) {
            setting(['pexels_api_key' => $request->pexels_api_key])->save();
        }

        return response()->json([], 200);
    }

    public function pixabayapi(Request $request)
    {
        return view('panel.admin.settings.pixabay');
    }

    public function pixabayapiTest()
    {
        $client = new Client;
        $api = setting('pixabay_api_key');
        if ($api == '') {
            echo 'You must provide Pixabay API Key.';

            return;
        }

        $apiKey = $api;
        $client = new Client;

        try {
            $response = $client->get("https://pixabay.com/api/?key=$apiKey&q=Google");
            echo ' <br>' . $apiKey . ' - SUCCESS <br>';
        } catch (Exception $e) {
            echo $e->getMessage() . ' - ' . $apiKey . ' -FAILED <br>';
        }
    }

    public function pixabayapiSave(Request $request)
    {
        if (Helper::appIsNotDemo()) {
            setting(['pixabay_api_key' => $request->pixabay_api_key])->save();
        }

        return response()->json([], 200);
    }

    public function serperapi(Request $request)
    {
        return view('panel.admin.settings.serperapi');
    }

    public function perplexity(Request $request)
    {
        return view('panel.admin.settings.perplexity');
    }

    public function serperapiSave(Request $request)
    {
        $settings = SettingTwo::getCache();
        if (Helper::appIsNotDemo()) {
            $settings->serper_api_key = $request->serper_api_key;
            $settings->save();

            if ($request->hasAny(['serper_seo_tool_improve', 'serper_seo_aw_anlyze', 'seo_ai_tool', 'serper_seo_aw_improve', 'serper_seo_aw_sq', 'serper_seo_aw_keyword', 'serper_seo_blog_title_desc', 'serper_seo_site_meta'])) {
                setting([
                    'serper_seo_aw_sq'           => $request->serper_seo_aw_sq,
                    'serper_seo_aw_keyword'      => $request->serper_seo_aw_keyword,
                    'serper_seo_blog_title_desc' => $request->serper_seo_blog_title_desc,
                    'serper_seo_site_meta'       => $request->serper_seo_site_meta,
                    'serper_seo_aw_anlyze'       => $request->serper_seo_aw_anlyze,
                    'serper_seo_aw_improve'      => $request->serper_seo_aw_improve,
                    'seo_ai_tool'                => $request->seo_ai_tool,
                    'serper_seo_tool_improve'    => $request->serper_seo_tool_improve,
                ])->save();
            }
        }

        return response()->json([], 200);
    }

    public function perplexitySave(Request $request)
    {
        if (Helper::appIsNotDemo()) {
            setting([
                'perplexity_key' => $request->perplexity_key,
            ])->save();
        }

        return back()->with(['message' => __('Updated Successfully'), 'type' => 'success']);
    }

    public function serperapiTest()
    {
        try {
            $settings = SettingTwo::getCache();
            if ($settings->serper_api_key == '') {
                echo 'You must provide Serper API Key.';

                return;
            }
            $client = new Client;
            $response = $client->post('https://google.serper.dev/search', [
                'headers' => [
                    'X-API-KEY'    => $settings->serper_api_key,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'q' => 'Coffee',
                ],
            ]);
            $responseData = json_decode($response->getBody(), true);
            echo ' <br>' . $settings->serper_api_key . ' - SUCCESS <br><hr> Example about "Coffee": <br>' . $responseData['organic'][0]['snippet'] . '<br>';
        } catch (Exception $e) {
            echo $e->getMessage() . ' - ' . $settings->serper_api_key . ' -FAILED <br>';
        }
    }

    public function xAiTest()
    {
        if (Helper::appIsDemo()) {
            return to_route('dashboard.user.index')->with([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }
        $settings = Setting::getCache();
        $apiKeys = ApiHelper::setXAiKey($settings, true);
        foreach ($apiKeys as $apiKey) {
            try {
                $history[] = ['role' => 'system', 'content' => 'Your name is Grok AI and you are a helpful assistant.'];
                $history[] = ['role' => 'user', 'content' => 'What is your name?'];
                $cli = OpenAI::factory()->withBaseUri('https://api.x.ai/v1')
                    ->withHttpHeader('Authorization', 'Bearer ' . $apiKey)
                    ->withHttpHeader('Content-Type', 'application/json')
                    ->withApiKey($apiKey)
                    ->make();

                $c = $cli->chat()->create(
                    [
                        'messages'    => $history,
                        'model'       => EntityEnum::GROK_2_1212->value,
                        'stream'      => false,
                        'temperature' => 0,
                    ]
                );
                echo '<hr>' . $c['choices'][0]['message']['content'];
                echo '<br>' . $apiKey . ' - SUCCESS <br>';
            } catch (Exception|Throwable $e) {
                echo __('Something went wrong. Please check your API key or Credit balance.') . ' <br> ' . $apiKey . ' -FAILED <br>';
            }
        }
    }

    public function openaiTest()
    {
        if (Helper::appIsDemo()) {
            return to_route('dashboard.user.index')->with([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }
        $settings = Setting::getCache();
        $apiKeys = ApiHelper::setOpenAiKey($settings, true);
        foreach ($apiKeys as $apiKey) {

            $client = new Client([
                'base_uri' => 'https://api.openai.com/v1/',
                'headers'  => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type'  => 'application/json',
                ],
            ]);

            try {

                $response = $client->post('chat/completions', [
                    'json' => [
                        'model'       => EntityEnum::GPT_3_5_TURBO->value,
                        'messages'    => [['role' => 'user', 'content' => 'Say this is a test!']],
                        'temperature' => 0.7,
                    ],
                ]);

                echo ' <br>' . $apiKey . ' - SUCCESS <br>';
            } catch (Exception $e) {
                echo $e->getMessage() . ' - ' . $apiKey . ' -FAILED <br>';
            }
        }
    }

    public function stablediffusionTest(): void
    {
        $client = new Client;
        $settings = SettingTwo::getCache();
        if ($settings->stable_diffusion_api_key == '') {
            echo 'You must provide Stable Difussion API key.';

            return;
        }

        $apiKeys = explode(',', $settings->stable_diffusion_api_key);

        foreach ($apiKeys as $apiKey) {

            $client = new Client([
                'base_uri' => 'https://stablediffusionapi.com',
                'headers'  => [
                    'Content-Type' => 'application/json',
                ],
            ]);
            $prompt = 'Man on the mountain';

            try {
                // print_r($client); exit;
                $response = $client->post('/api/v3/text2img', [
                    'json' => [
                        'key'                 => $apiKey,
                        'prompt'              => $prompt,
                        'negative_prompt'     => null,
                        'width'               => 512,
                        'height'              => 512,
                        'samples'             => 1,
                        'num_inference_steps' => '20',
                        'seed'                => null,
                        'guidance_scale'      => 7.5,
                        'safety_checker'      => 'yes',
                        'multi_lingual'       => 'no',
                        'panorama'            => 'no',
                        'self_attention'      => 'no',
                        'upscale'             => 'no',
                        'embeddings_model'    => null,
                        'webhook'             => null,
                        'track_id'            => null,
                    ],
                ]);
                echo ' <br>' . $apiKey . ' - SUCCESS <br>';
            } catch (Exception $e) {
                echo $e->getMessage() . ' - ' . $apiKey . ' -FAILED <br>';
            }
        }
    }

    public function xAiSave(Request $request): JsonResponse
    {
        if (Helper::appIsNotDemo()) {
            setting([
                'xai_api_secret'    => $request->xai_api_secret,
                'xai_default_model' => $request->xai_default_model,
            ])->save();
        }

        return response()->json([], 200);
    }

    public function openaiSave(Request $request): JsonResponse
    {
        $settings = Setting::getCache();
        $settings_two = SettingTwo::getCache();
        if (Helper::appIsNotDemo()) {
            $settings->openai_api_secret = $request->openai_api_secret;
            $settings->openai_default_model = $request->openai_default_model;
            $settings->openai_default_language = $request->openai_default_language;
            $settings->openai_default_tone_of_voice = $request->openai_default_tone_of_voice;
            $settings->openai_default_creativity = $request->openai_default_creativity;
            $settings->openai_max_input_length = $request->openai_max_input_length;
            $settings->openai_max_output_length = $request->openai_max_output_length;
            $settings_two->dalle = $request->dalle_default_model;
            $settings_two->openai_default_stream_server = $request->openai_default_stream_server;
            $settings->save();
            $settings_two->save();
            setting(key: [
                'hide_creativity_option'    => $request->hide_creativity_option,
                'hide_tone_of_voice_option' => $request->hide_tone_of_voice_option,
                'hide_output_length_option' => $request->hide_output_length_option,
                'dalle_hidden'              => $request->dalle_hidden,
                'realtime_voice_chat'       => $request->realtime_voice_chat,
                'openai_file_search'        => $request->openai_file_search,
            ])->save();

            if (MarketplaceHelper::isRegistered('chatbot')) {
                \App\Extensions\Chatbot\System\Models\Chatbot::query()->update([
                    'ai_model' => $request->openai_default_model,
                ]);
            }
        }
        app(MenuService::class)->regenerate();

        return response()->json([], 200);
    }

    public function affiliateStatusSave($id, Request $request): JsonResponse
    {
        if (Helper::appIsNotDemo()) {
            $user = User::find($id);
            if ($user) {
                $user->affiliate_status = $request->input('affiliate_status');
                $user->save();
            }

            app(MenuService::class)->regenerate();
        }

        return response()->json([], 200);
    }

    public function stablediffusionSave(Request $request): JsonResponse
    {
        $settings = SettingTwo::getCache();
        if (Helper::appIsNotDemo()) {
            $settings->stable_diffusion_api_key = $request->stable_diffusion_api_key;
            $settings->stablediffusion_default_language = $request->stablediffusion_default_language;
            $settings->stablediffusion_default_model = $request->stablediffusion_default_model;

            if ($request->has('stablediffusion_bedrock_model')) {
                $settings->stablediffusion_bedrock_model = $request->stablediffusion_bedrock_model;
            }

            $settings->save();

            setting([
                'stable_hidden' => $request->stable_hidden,
            ])->save();
        }

        return response()->json([], 200);
    }

    // thumbnail
    public function thumbnail()
    {
        return view('panel.admin.settings.thumbnail');
    }

    // thumbnailSave
    public function thumbnailSave(Request $request)
    {
        if (Helper::appIsNotDemo()) {
            setting(['image_thumbnail' => $request->image_thumbnail])->save();
        }

        return response()->json([], 200);
    }

    // thumbnailPurge
    public function thumbnailPurge()
    {
        if (Helper::appIsNotDemo()) {
            PurgeThumbImages();
        }

        return response()->json([], 200);
    }

    public function tts()
    {
        return view('panel.admin.settings.tts');
    }

    public function aimlapi(): View
    {
        return view('panel.admin.settings.aimlapi');
    }

    public function aimlapiSave(Request $request): JsonResponse
    {
        $settings = Setting::getCache();

        if (Helper::appIsNotDemo()) {
            $settings->update([
                'aimlapi_key'    => $request->aimlapi_key,
                'ai_music_model' => $request->ai_music_model,
            ]);
        }

        return response()->json([], 200);
    }

    public function ttsSave(Request $request)
    {
        $settings = Setting::getCache();
        $settings_two = SettingTwo::getCache();
        if (Helper::appIsNotDemo()) {
            $settings->gcs_file = $request->gcs_file;
            $settings->gcs_name = $request->gcs_name;

            $settings_two->elevenlabs_api_key = $request->elevenlabs_api_key;

            $settings_two->feature_tts_google = $request->feature_tts_google;
            $settings_two->feature_tts_openai = $request->feature_tts_openai;
            $settings_two->feature_tts_elevenlabs = $request->feature_tts_elevenlabs;

            if ($request->hasAny(['feature_tts_azure', 'azure_api_key', 'azure_region'])) {
                setting([
                    'feature_tts_azure' => $request->get('feature_tts_azure'),
                    'azure_api_key'     => $request->get('azure_api_key'),
                    'azure_region'      => $request->get('azure_region'),
                ])->save();
            }

            if ($request->hasAny(['speechify_api_key', 'feature_tts_speechify'])) {
                setting([
                    'feature_tts_speechify' => $request->get('feature_tts_speechify'),
                    'speechify_api_key'     => $request->get('speechify_api_key'),
                ])->save();
            }

            $settings->save();
            $settings_two->save();
        }

        return response()->json([], 200);
    }

    public function payment()
    {
        return view('panel.admin.settings.stripe');
    }

    public function paymentSave(Request $request)
    {
        if (Helper::appIsNotDemo()) {
            $settings = Setting::getCache();
            $settings->default_currency = $request->default_currency;
            $settings->stripe_active = 1;
            $settings->stripe_key = $request->stripe_key;
            $settings->stripe_secret = $request->stripe_secret;
            $settings->stripe_base_url = $request->stripe_base_url;
            $settings->save();
        }

        return response()->json([], 200);
    }

    public function privacy()
    {
        return view('panel.admin.settings.privacy');
    }

    public function privacySave(Request $request)
    {
        if (Helper::appIsNotDemo()) {

            $settings_two = SettingTwo::getCache();
            $settings = Setting::getCache();

            $termsLocal = $request->termsLocal;
            $privacyLocal = $request->privacyLocal;

            if ($termsLocal == $settings_two->languages_default) {
                $settings->terms_content = $request->terms_content;
            } else {
                $terms = PrivacyTerms::where('type', 'terms')->where('lang', $termsLocal)->first();
                if ($terms) {
                    $terms->content = $request->terms_content;
                    $terms->save();
                } else {
                    $newTerms = new PrivacyTerms;
                    $newTerms->type = 'terms';
                    $newTerms->lang = $termsLocal;
                    $newTerms->content = $request->terms_content;
                    $newTerms->save();
                }
            }

            if ($privacyLocal == $settings_two->languages_default) {
                $settings->privacy_content = $request->privacy_content;
            } else {
                $privacy = PrivacyTerms::where('type', 'privacy')->where('lang', $privacyLocal)->first();
                if ($privacy) {
                    $privacy->content = $request->privacy_content;
                    $privacy->save();
                } else {
                    $newPrivacy = new PrivacyTerms;
                    $newPrivacy->type = 'privacy';
                    $newPrivacy->lang = $privacyLocal;
                    $newPrivacy->content = $request->privacy_content;
                    $newPrivacy->save();
                }
            }

            $settings->privacy_enable = $request->privacy_enable;
            $settings->privacy_enable_login = $request->privacy_enable_login;
            $settings->save();

        }

        return response()->json([], 200);
    }

    public function getPrivacyTermsContent(Request $request)
    {
        $type = $request->input('type');
        $language = $request->input('lang');
        $settings_two = SettingTwo::getCache();

        if ($settings_two->languages_default == $language) {

            $settings = Setting::getCache();
            $content = [
                'type'    => $type,
                'lang'    => $language,
                'content' => $type == 'terms' ? $settings->terms_content : $settings->privacy_content,
            ];

        } else {
            $privacy_terms = PrivacyTerms::where('type', $type)->where('lang', $language)->first();
            $content = [
                'type'    => $privacy_terms?->type ?? $type,
                'lang'    => $privacy_terms?->lang,
                'content' => $privacy_terms?->content,
            ];
        }

        return response()->json($content);
    }

    public function getMetaContent(Request $request)
    {
        $type = $request->input('type');
        $language = $request->input('lang');
        $settings_two = SettingTwo::getCache();

        if ($settings_two->languages_default == $language) {

            $settings = Setting::getCache();
            $content = [
                'type'    => $type,
                'lang'    => $language,
                'content' => $type == 'meta_title' ? $settings->meta_title : $settings->meta_description,
            ];

        } else {
            $meta = PrivacyTerms::where('type', $type)->where('lang', $language)->first();
            $content = [
                'type'    => $meta?->type ?? $type,
                'lang'    => $meta?->lang,
                'content' => $meta?->content,
            ];
        }

        return response()->json($content);
    }
}
