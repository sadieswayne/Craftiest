<?php

namespace App\Helpers\Classes;

use App\Models;
use App\Models\OpenAIGenerator;
use App\Models\OpenaiGeneratorChatCategory;
use App\Services\Common\MenuService;
use Database\Seeders\AdminPermissionSeeder;
use Database\Seeders\EngineSeeder;
use Database\Seeders\EntitySeeder;
use Database\Seeders\IntroductionSeeder;
use Database\Seeders\MenuSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SocialAccountsSeeder;
use Database\Seeders\TokenSeeder;
use Database\Seeders\VoiceIsolatorSeeder;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RachidLaasri\LaravelInstaller\Repositories\ApplicationStatusRepositoryInterface;

class InstallationHelper
{
    public static function runInstallation(?bool $check = true): void
    {
        $installationData = self::data($check);

        foreach ($installationData as $key => $value) {
            if (isset($value['table'])) {

                if (is_string($value['table'])) {
                    if (! Schema::hasTable($value['table'])) {
                        continue;
                    }
                } else {
                    if (! $value['table']) {
                        continue;
                    }
                }

                if (! isset($value['sql'])) {
                    continue;
                }

                foreach ($value['sql'] as $sqlData) {
                    if (isset($sqlData['condition'])) {
                        if ($sqlData['condition']) {

                            $files = $sqlData['files'] ?? [];

                            if (is_array($files)) {
                                foreach ($files as $file) {
                                    DB::unprepared(
                                        file_get_contents(
                                            resource_path($file)
                                        )
                                    );
                                }
                            }
                        }
                    }

                    if (isset($sqlData['callback'])) {

                        if (isset($sqlData['condition'])) {
                            $condition = $sqlData['condition'];
                        } else {
                            $condition = true;
                        }

                        if ($condition) {
                            $callback = $sqlData['callback'];
                            if (is_callable($callback)) {
                                call_user_func($callback);
                            }
                        }
                    }
                }
            } else {
                // if there is a different variation.
            }
        }

        Artisan::call('optimize:clear');

        DB::beginTransaction();
    }

    public static function data(?bool $check = true): array
    {
        return [
            [
                'table' => 'openai_filters',
                'sql'   => [
                    [
                        'condition' => DB::table('openai_filters')->count() === 0,
                        'files'     => [
                            'dev_tools/openai_filters.sql',
                        ],
                    ],
                ],
            ],
            [
                'table' => 'openai',
                'sql'   => [
                    [
                        'condition' => Models\OpenAIGenerator::count() === 0,
                        'files'     => [
                            'dev_tools/openai_table.sql',
                        ],
                    ],
                ],
            ],
            [
                'table' => 'openai',
                'sql'   => [
                    [
                        'condition' => false,
                        'files'     => [
                            'dev_tools/new_openai_table_templates.sql',
                        ],
                    ],
                ],
            ],
            [
                'table' => 'frontend_tools',
                'sql'   => [
                    [
                        'condition' => Models\FrontendTools::count() === 0,
                        'files'     => [
                            'dev_tools/frontend_tools.sql',
                        ],
                    ],
                ],
            ],
            [
                'table' => 'faq',
                'sql'   => [
                    [
                        'condition' => Models\Faq::count() === 0,
                        'files'     => [
                            'dev_tools/faq.sql',
                        ],
                    ],
                ],
            ],
            [
                'table' => 'frontend_future',
                'sql'   => [
                    [
                        'condition' => Models\FrontendFuture::count() === 0,
                        'files'     => [
                            'dev_tools/frontend_future.sql',
                        ],
                    ],
                ],
            ],
            [
                'table' => 'howitworks',
                'sql'   => [
                    [
                        'condition' => Models\HowitWorks::count() === 0,
                        'files'     => [
                            'dev_tools/howitworks.sql',
                        ],
                    ],
                ],
            ],
            [
                'table' => 'testimonials',
                'sql'   => [
                    [
                        'condition' => Models\Testimonials::count() === 0,
                        'files'     => [
                            'dev_tools/testimonials.sql',
                        ],
                    ],
                ],
            ],
            [
                'table' => 'frontend_who_is_for',
                'sql'   => [
                    [
                        'condition' => Models\FrontendForWho::count() === 0,
                        'files'     => [
                            'dev_tools/frontend_who_is_for.sql',
                        ],
                    ],
                ],
            ],
            [
                'table' => 'frontend_generators',
                'sql'   => [
                    [
                        'condition' => Models\FrontendGenerators::count() === 0,
                        'files'     => [
                            'dev_tools/frontend_generators.sql',
                        ],
                    ],
                ],
            ],
            [
                'table' => 'clients',
                'sql'   => [
                    [
                        'condition' => Models\Clients::count() === 0,
                        'files'     => [
                            'dev_tools/clients.sql',
                        ],
                    ],
                ],
            ],
            [
                'table' => 'chatbot',
                'sql'   => [
                    [
                        'condition' => Models\Chatbot\Chatbot::count() === 0,
                        'files'     => [
                            'dev_tools/chatbot.sql',
                        ],
                    ],
                ],
            ],
            [
                'table' => 'email_templates',
                'sql'   => [
                    [
                        'condition' => Models\EmailTemplates::count() === 0,
                        'files'     => [
                            'dev_tools/email_templates.sql',
                        ],
                    ],
                ],
            ],
            [
                'table' => 'ads',
                'sql'   => [
                    [
                        'condition' => Models\Ad::count() === 0,
                        'files'     => [
                            'dev_tools/ads.sql',
                        ],
                    ],
                ],
            ],
            [
                'table' => 'openai',
                'sql'   => [
                    [
                        'condition' => Models\OpenAIGenerator::where('slug', 'ai_article_wizard_generator')->count() === 0,
                        'files'     => [
                            'dev_tools/ai_wizard.sql',
                        ],
                    ],
                    [
                        'condition' => Models\OpenAIGenerator::where('slug', 'ai_vision')->count() === 0,
                        'files'     => [
                            'dev_tools/ai_vision.sql',
                        ],
                    ],
                    [
                        'condition' => Models\OpenAIGenerator::where('slug', 'ai_pdf')->count() === 0,
                        'files'     => [
                            'dev_tools/ai_pdf.sql',
                        ],
                    ],
                    [
                        'condition' => Models\OpenAIGenerator::where('slug', 'ai_chat_image')->count() === 0,
                        'files'     => [
                            'dev_tools/ai_chat_image.sql',
                        ],
                    ],
                    [
                        'condition' => Models\OpenAIGenerator::where('slug', 'ai_rewriter')->count() === 0,
                        'files'     => [
                            'dev_tools/ai_rewriter.sql',
                        ],
                    ],
                    [
                        'condition' => Models\OpenAIGenerator::where('slug', 'ai_webchat')->count() === 0,
                        'files'     => [
                            'dev_tools/ai_webchat.sql',
                        ],
                    ],
                    [
                        'condition' => Models\OpenAIGenerator::where('slug', 'ai_pdf')->count() === 0,
                        'files'     => [
                            'dev_tools/ai_filechat.sql',
                        ],
                    ],
                    [
                        'condition' => Models\OpenAIGenerator::where('slug', 'ai_video')->count() === 0,
                        'files'     => [
                            'dev_tools/ai_video.sql',
                        ],
                    ],
                ],
            ],
            [
                'table' => 'openai_chat_category',
                'sql'   => [
                    [
                        'condition' => Models\OpenaiGeneratorChatCategory::count() === 0,
                        'files'     => [
                            'dev_tools/openai_chat_categories_table.sql',
                        ],
                    ],
                    [
                        'condition' => Models\OpenaiGeneratorChatCategory::where('slug', 'ai_vision')->count() === 0,
                        'files'     => [
                            'dev_tools/ai_vision2.sql',
                        ],
                    ],
                    [
                        'condition' => Models\OpenaiGeneratorChatCategory::where('slug', 'ai_pdf')->count() === 0,
                        'files'     => [
                            'dev_tools/ai_pdf2.sql',
                            'dev_tools/ai_filechat2.sql',
                        ],
                    ],
                    [
                        'condition' => Models\OpenaiGeneratorChatCategory::where('slug', 'ai_chat_image')->count() === 0,
                        'files'     => [
                            'dev_tools/ai_chat_image2.sql',
                        ],
                    ],
                    [
                        'condition' => Models\OpenaiGeneratorChatCategory::where('slug', 'ai_webchat')->count() === 0,
                        'files'     => [
                            'dev_tools/ai_webchat2.sql',
                        ],
                    ],
                ],
            ],
            [
                'table' => 'email_templates',
                'sql'   => [
                    [
                        'condition' => Models\EmailTemplates::count() === 0,
                        'files'     => [
                            'dev_tools/team_email_templates.sql',
                        ],
                    ],
                ],
            ],
            [
                'table' => 'email_templates',
                'sql'   => [
                    [
                        'condition' => Models\EmailTemplates::count() === 0,
                        'files'     => [
                            'dev_tools/subscription_and_payment_email_templates.sql',
                        ],
                    ],
                ],
            ],
            [
                'table' => 'plans',
                'sql'   => [
                    [
                        'condition' => Schema::hasColumn('plans', 'open_ai_items') && Schema::hasTable('openai'),
                        'callback'  => function () {
                            $openaiItems = Models\OpenAIGenerator::query()->pluck('slug')->toArray();

                            $plans = Models\Plan::query()->get();

                            foreach ($plans as $plan) {
                                $plan->open_ai_items = $openaiItems;
                                $plan->save();
                            }
                        },
                    ],
                ],
            ],
            [
                'table' => 'plans',
                'sql'   => [
                    [
                        'condition' => Schema::hasColumn('plans', 'hidden_url'),
                        'callback'  => function () {
                            $plans = Models\Plan::whereNotNull('hidden_url')->get();

                            foreach ($plans as $plan) {
                                $newUrl = Str::replace('/dashboard/user/', '/plan/', $plan->hidden_url);

                                if ($newUrl !== $plan->hidden_url) {
                                    $plan->update(['hidden_url' => $newUrl]);
                                }
                            }
                        },
                    ],
                ],
            ],
            [
                'table' => 'openai',
                'sql'   => [
                    [
                        'condition' => Schema::hasTable('settings') && Schema::hasColumn('settings', 'free_open_ai_items'),
                        'callback'  => function () {
                            $openaiItems = Models\OpenAIGenerator::query()->pluck('slug')->toArray();
                            $setting = Models\Setting::first();

                            $setting->update([
                                'free_open_ai_items' => $openaiItems ?: [],
                            ]);
                        },
                    ],
                ],
            ],
            [
                'table' => (new Models\Page)->getTable(),
                'sql'   => [
                    [
                        'condition' => Models\Page::where('is_custom', 1)->count() === 0,
                        'files'     => [
                            'dev_tools/inner_pages.sql',
                        ],
                    ],
                ],
            ],
            [
                'table' => (new Models\Currency)->getTable(),
                'sql'   => [
                    [
                        'condition' => Models\Currency::count() === 0,
                        'files'     => [
                            'dev_tools/currency.sql',
                        ],
                    ],
                ],
            ],
            [
                'table' => 'openai',
                'sql'   => [
                    [
                        'condition' => ! OpenAIGenerator::where('slug', 'ai_voiceover')->exists(),
                        'files'     => [
                            'dev_tools/ai_voiceover.sql',
                            'dev_tools/ai_filter_voiceover.sql',
                        ],
                    ],
                ],
            ],
            [
                'table' => 'openai',
                'sql'   => [
                    [
                        'condition' => ! OpenAIGenerator::where('slug', 'ai_youtube')->exists(),
                        'files'     => [
                            'dev_tools/ai_youtube.sql',
                            'dev_tools/ai_filter_youtube.sql',
                        ],
                    ],
                ],
            ],
            [
                'table' => 'openai',
                'sql'   => [
                    [
                        'condition' => ! OpenAIGenerator::where('slug', 'ai_rss')->exists(),
                        'files'     => [
                            'dev_tools/ai_rss.sql',
                            'dev_tools/ai_filter_rss.sql',
                        ],
                    ],
                ],
            ],
            [
                'table' => 'integrations',
                'sql'   => [
                    [
                        'condition' => Models\Integration\Integration::query()->where('slug', 'wordpress')->doesntExist(),
                        'files'     => [
                            'dev_tools/integrations/wordpress.sql',
                        ],
                    ],
                ],
            ],
            [
                'table' => 'settings_two',
                'sql'   => [
                    [
                        'condition' => Models\SettingTwo::query()->whereNotNull('liquid_license_domain_key')->exists() && $check,
                        'callback'  => function () {
                            try {
                                $check = Helper::settingTwo('liquid_license_domain_key');

                                if ($check) {
                                    app(ApplicationStatusRepositoryInterface::class)->check(
                                        $check, true
                                    );
                                }
                            } catch (Exception $e) {
                            }
                        },
                    ],
                ],
            ],
            [
                'table' => 'menus',
                'sql'   => [
                    [
                        'condition' => true,
                        'callback'  => function () {
                            try {
                                app(MenuSeeder::class)->run();

                                Models\Common\Menu::query()->where('key', 'config')->update([
                                    'order' => 0,
                                ]);

                                Models\Common\Menu::query()->where('key', 'cloudflare_r2_extension')->update([
                                    'order' => 1,
                                ]);
                                Models\Common\Menu::query()->where('key', 'maintenance_setting')->update([
                                    'order' => 2,
                                ]);
                                Models\Common\Menu::query()->where('key', 'ai_chat_models')->update([
                                    'order' => 3,
                                ]);
                                Models\Common\Menu::query()->where('key', 'ext_chat_bot')->update([
                                    'label' => 'AI Bots',
                                    'order' => 2,
                                ]);
                                Models\Common\Menu::query()->where('key', 'ext_chat_bot_agent')->update([
                                    'label' => 'Human Agent',
                                ]);
                            } catch (Exception $exception) {
                            }
                        },
                    ],
                ],
            ],
            [
                'table' => 'entities',
                'sql'   => [
                    [
                        'condition' => true,
                        'callback'  => function () {
                            try {
                                app(EntitySeeder::class)->run();
                            } catch (Exception $exception) {
                            }
                        },
                    ],
                ],
            ],
            [
                'table' => 'engines',
                'sql'   => [
                    [
                        'condition' => true,
                        'callback'  => function () {
                            try {
                                app(EngineSeeder::class)->run();
                            } catch (Exception $exception) {
                            }
                        },
                    ],
                ],
            ],
            [
                'table' => 'tokens',
                'sql'   => [
                    [
                        'condition' => true,
                        'callback'  => function () {
                            try {
                                app(TokenSeeder::class)->run();
                            } catch (Exception $exception) {
                            }
                        },
                    ],
                ],
            ],
            [
                'table' => 'social_media_accounts',
                'sql'   => [
                    [
                        'condition' => true,
                        'callback'  => function () {
                            try {
                                app(SocialAccountsSeeder::class)->run();
                            } catch (Exception $exception) {
                            }
                        },
                    ],
                ],
            ],
            [
                'table' => 'menus',
                'sql'   => [
                    [
                        'condition' => true,
                        'callback'  => function () {
                            try {
                                Models\Common\Menu::query()
                                    ->where([
                                        'key' => 'chat_training_extension',
                                    ])->update([
                                        'label' => 'Chatbot Training',
                                    ]);
                                Models\Common\Menu::query()
                                    ->where([
                                        'key' => 'api_integration',
                                    ])->update([
                                        'route' => 'default',
                                    ]);

                                Models\Common\Menu::query()
                                    ->where([
                                        'key' => 'ai_chat_all',
                                    ])->update([
                                        'route' => 'dashboard.user.openai.chat.chat',
                                    ]);

                                Models\Common\Menu::query()
                                    ->where([
                                        'key' => 'membership_plans',
                                    ])->update([
                                        'label' => 'Membership Plans (old version)',
                                    ]);

                                Models\Common\Menu::query()
                                    ->where([
                                        'key' => 'admin_finance_plan',
                                    ])->update([
                                        'label' => 'Membership Plans',
                                    ]);

                                Models\Common\Menu::query()
                                    ->where([
                                        'key' => 'storage',
                                    ])->delete();

                                Models\Common\Menu::query()
                                    ->where([
                                        'key' => 'languages',
                                    ])->delete();

                                Models\Common\Menu::query()
                                    ->where([
                                        'key' => 'smtp',
                                    ])->delete();

                                Models\Common\Menu::query()
                                    ->where([
                                        'key' => 'gdpr',
                                    ])->delete();

                                Models\Common\Menu::query()
                                    ->where([
                                        'key' => 'affiliate_setting',
                                    ])->delete();

                                Models\Common\Menu::query()
                                    ->where([
                                        'key' => 'invoice',
                                    ])->delete();

                                Models\Common\Menu::query()
                                    ->where([
                                        'key' => 'general',
                                    ])->delete();

                                Models\Common\Menu::query()
                                    ->where([
                                        'key' => 'ai_fall',
                                    ])->delete();

                                Models\Common\Menu::query()
                                    ->where([
                                        'key' => 'google_adsense',
                                    ])->delete();

                                Models\Common\Menu::query()
                                    ->where([
                                        'key' => 'ai_avatar_pro',
                                    ])->delete();

                                Models\Common\Menu::query()
                                    ->where([
                                        'key' => 'introductions',
                                    ])->delete();

                                Models\Common\Menu::query()
                                    ->where([
                                        'key' => 'api_cost_management',
                                    ])->delete();

                                $apiIntegration = Models\Common\Menu::query()
                                    ->where([
                                        'key' => 'api_integration',
                                    ])->first();

                                Models\Common\Menu::query()->where('key', 'photo_studio_setting')->update([
                                    'parent_id' => $apiIntegration->id,
                                ]);

                                Models\Common\Menu::query()->where('key', 'admin_finance_plan')->update([
                                    'label' => 'Pricing Plans',
                                ]);

                                $setting = Models\Common\Menu::query()
                                    ->where([
                                        'key' => 'settings',
                                    ])->first();

                                Models\Common\Menu::query()->where('key', 'ai_chat_models')->update([
                                    'parent_id' => $setting->id,
                                    'label'     => 'AI Models',
                                ]);

                                Models\Common\Menu::query()->where('key', 'api_integration_fal_ai')->update([
                                    'label'     => 'Fal AI',
                                ]);

                                app(MenuService::class)->regenerate();
                            } catch (Exception $exception) {
                            }
                        },
                    ],
                ],
            ],
            [
                'table' => 'introductions',
                'sql'   => [
                    [
                        'condition' => true,
                        'callback'  => function () {
                            try {
                                app(IntroductionSeeder::class)->run();
                            } catch (Exception $exception) {
                            }
                        },
                    ],
                ],
            ],
            [
                'table' => 'permissions',
                'sql'   => [
                    [
                        'condition' => true,
                        'callback'  => function () {
                            try {
                                app(PermissionSeeder::class)->run();
                            } catch (Exception $exception) {
                            }
                        },
                    ],
                ],
            ],
            [
                'table' => 'role_has_permissions',
                'sql'   => [
                    [
                        'condition' => true,
                        'callback'  => function () {
                            try {
                                app(AdminPermissionSeeder::class)->run();
                            } catch (Exception $exception) {
                            }
                        },
                    ],
                ],
            ],
            [
                'table' => 'roles',
                'sql'   => [
                    [
                        'condition' => true,
                        'callback'  => function () {
                            try {
                                app(RoleSeeder::class)->run();
                            } catch (Exception $exception) {
                            }
                        },
                    ],
                ],
            ],
            [
                'table' => 'entities',
                'sql'   => [
                    [
                        'condition' => true,
                        'callback'  => function () {
                            try {
                                $models = \App\Domains\Entity\Models\Entity::query()->get();

                                foreach ($models as $model) {
                                    $model->update(['selected_title' => $model->getAttribute('title')]);
                                }
                            } catch (Exception $exception) {
                            }
                        },
                    ],
                ],
            ],
            [
                'table' => 'frontend_sections_statuses_titles',
                'sql'   => [
                    [
                        'condition' => Schema::hasColumn('frontend_sections_statuses_titles', 'marquee_items'),
                        'callback'  => function () {
                            try {
                                // fill the column with the default value if it's empty
                                Models\Frontend\FrontendSectionsStatus::query()->update([
                                    'marquee_items' => 'Cold Email,Newsletter,Summarize,Product Description,Testimonial,Pick an outfit,Study Vocabulary, Create a workout plan,Transcribe my class notes,Create a pros and cons list,Morning Productivity Plan,Experience Tokyo like a local,Translate',
                                ]);
                            } catch (Exception $exception) {
                            }
                        },
                    ],
                ],
            ],
            [
                'table' => 'openai_chat_category',
                'sql'   => [
                    [
                        'condition' => true,
                        'callback'  => function () {
                            try {
                                // fill the column with the default value if it's empty

                                $data = OpenaiGeneratorChatCategory::query()
                                    ->where('slug', 'ai_pdf')
                                    ->first();

                                if ($data) {
                                    $description = $data->description;
                                    $helps_with = $data->helps_with;

                                    $description = str_replace('CSV', 'CSX, XLS, XLSX', $description);
                                    $helps_with = str_replace('CSV', 'CSX, XLS, XLSX', $helps_with);

                                    $data->update([
                                        'description' => $description,
                                        'helps_with'  => $helps_with,
                                    ]);
                                }

                            } catch (Exception $exception) {
                            }
                        },
                    ],
                ],
            ],
            [
                'table' => 'advanced_features_section',
                'sql'   => [
                    [
                        'condition' => Models\Section\AdvancedFeaturesSection::query()->count() == 0,
                        'callback'  => function () {
                            try {
                                // default data
                                $defaultData = [
                                    [
                                        'title'       => __('Article Wizard'),
                                        'description' => __('Create a social media post and schedule it to be published directly on Linkedin or X.'),
                                        'image'       => custom_theme_url('/assets/landing-page/advanced-feature-1.png'),
                                    ],
                                    [
                                        'title'       => __('Intelligent AI Assistant'),
                                        'description' => __('Create a social media post and schedule it to be published directly on Linkedin or X.'),
                                        'image'       => custom_theme_url('/assets/landing-page/advanced-feature-1.png'),
                                    ],
                                    [
                                        'title'       => __('Publish on Social Media'),
                                        'description' => __('Create a social media post and schedule it to be published directly on Linkedin or X.'),
                                        'image'       => custom_theme_url('/assets/landing-page/advanced-feature-1.png'),
                                    ],
                                    [
                                        'title'       => __('SEO Tool'),
                                        'description' => __('Create a social media post and schedule it to be published directly on Linkedin or X.'),
                                        'image'       => custom_theme_url('/assets/landing-page/advanced-feature-1.png'),
                                    ],
                                    [
                                        'title'       => __('Real-Time Data'),
                                        'description' => __('Create a social media post and schedule it to be published directly on Linkedin or X.'),
                                        'image'       => custom_theme_url('/assets/landing-page/advanced-feature-1.png'),
                                    ],
                                    [
                                        'title'       => __('AI Photo Editor'),
                                        'description' => __('Create a social media post and schedule it to be published directly on Linkedin or X.'),
                                        'image'       => custom_theme_url('/assets/landing-page/advanced-feature-1.png'),
                                    ],
                                ];
                                // fill the column with the default value if it's empty
                                Models\Section\AdvancedFeaturesSection::query()->insert($defaultData);
                            } catch (Exception $exception) {
                            }
                        },
                    ],
                ],
            ],
            [
                'table' => 'comparison_section_items',
                'sql'   => [
                    [
                        'condition' => Models\Section\ComparisonSectionItems::query()->count() == 0,
                        'callback'  => function () {
                            try {
                                // default data
                                $items = [
                                    ['label' => 'Multiple AI Tools', 'others' => false, 'ours' => true],
                                    ['label' => 'Custom Templates and Chatbot Personas', 'others' => false, 'ours' => true],
                                    ['label' => 'All-in-one Platform', 'others' => false, 'ours' => true],
                                    ['label' => 'Knows Your Brand', 'others' => false, 'ours' => true],
                                    ['label' => 'Intelligent AI Assistant', 'others' => false, 'ours' => true],
                                    ['label' => 'PrePaid', 'others' => false, 'ours' => true],
                                    ['label' => 'Lifetime Access', 'others' => false, 'ours' => true],
                                ];
                                // fill the column with the default value if it's empty
                                Models\Section\ComparisonSectionItems::query()->insert($items);
                            } catch (Exception $exception) {
                            }
                        },
                    ],
                ],
            ],
            [
                'table' => 'features_marquees',
                'sql'   => [
                    [
                        'condition' => Models\Section\FeaturesMarquee::query()->count() == 0,
                        'callback'  => function () {
                            try {
                                // default data
                                $items = [
                                    ['title' => 'Designed for mobile', 'position' => 'top'],
                                    ['title' => 'Easy to use', 'position' => 'top'],
                                    ['title' => 'Customizable', 'position' => 'top'],
                                    ['title' => 'No coding required', 'position' => 'top'],
                                    ['title' => '10 Reasons to use MagicAI', 'position' => 'bottom'],
                                    ['title' => 'No sign up required', 'position' => 'bottom'],
                                    ['title' => 'No watermarks', 'position' => 'bottom'],
                                    ['title' => 'No hidden fees', 'position' => 'bottom'],
                                ];
                                // fill the column with the default value if it's empty
                                Models\Section\FeaturesMarquee::query()->insert($items);
                            } catch (Exception $exception) {
                            }
                        },
                    ],
                ],
            ],
            [
                'table' => 'footer_items',
                'sql'   => [
                    [
                        'condition' => Models\Section\FooterItem::query()->count() === 0,
                        'callback'  => function () {
                            try {
                                $items = [
                                    ['item' => 'Premium Support 30-Day'],
                                    ['item' => 'Money Back Guarantee'],
                                    ['item' => 'Instant Access'],
                                    ['item' => 'Free Trial'],
                                    ['item' => 'Lifetime Updates'],
                                ];
                                // fill the column with the default value if it's empty
                                Models\Section\FooterItem::query()->insert($items);
                            } catch (Exception $exception) {
                            }
                        },
                    ],
                ],
            ],
            [
                'table' => 'banner_bottom_texts',
                'sql'   => [
                    [
                        'condition' => Models\Section\BannerBottomText::query()->count() == 0,
                        'callback'  => function () {
                            try {
                                $items = [
                                    ['text' => 'No Credit Card Required'],
                                    ['text' => 'Free Trial'],
                                    ['text' => '30 Day Money Back Guarentee'],
                                ];
                                // fill the column with the default value if it's empty
                                Models\Section\BannerBottomText::query()->insert($items);
                            } catch (Exception $exception) {
                            }
                        },
                    ],
                ],
            ],
            [
                'table' => 'plans',
                'sql'   => [
                    [
                        'condition' => true,
                        'callback'  => function () {
                            Models\Plan::query()
                                ->whereNull('plan_ai_tools')
                                ->whereNull('plan_features')
                                ->get()->map(callback: function ($plan) {
                                    $plan_ai_tools = Arr::pluck(MenuService::planAiToolsMenu(), 'key');

                                    $plan_features = Arr::pluck(MenuService::planFeatureMenu(), 'key');

                                    $plan->update([
                                        'plan_ai_tools' => $plan_ai_tools,
                                        'plan_features' => $plan_features,
                                    ]);
                                });
                        },
                    ],
                ],
            ],
            [
                'table' => 'openai',
                'sql'   => [
                    [
                        'condition' => ! OpenAIGenerator::where('slug', 'ai_voice_isolator')->exists(),
                        'callback'  => function () {
                            try {
                                app(VoiceIsolatorSeeder::class)->run();
                            } catch (Exception $exception) {
                            }
                        },
                    ],
                ],
            ],

            [
                'table' => 'menus',
                'sql'   => [
                    [
                        'condition' => Models\Common\Menu::query()->where('bolt_menu', true)->doesntExist(),
                        'callback'  => function () {
                            try {
                                $middle_nav_urls = [
                                    'dashboard' => [
                                        'background' => '#9A6FFD',
                                        'foreground' => '#fff',
                                    ],
                                    'ai_writer' => [
                                        'background' => '#468EA6',
                                        'foreground' => '#fff',
                                    ],
                                    'finance' => [
                                        'background' => '#3569F5',
                                        'foreground' => '#fff',
                                    ],
                                    'ai_editor' => [
                                        'background' => '#E29CB6',
                                        'foreground' => '#fff',
                                    ],
                                ];

                                foreach ($middle_nav_urls as $key => $value) {
                                    Models\Common\Menu::query()
                                        ->where('key', $key)
                                        ->update([
                                            'bolt_menu'       => true,
                                            'bolt_background' => $value['background'],
                                            'bolt_foreground' => $value['foreground'],
                                        ]);
                                }

                                app(MenuService::class)->regenerate();
                            } catch (Exception $exception) {
                            }
                        },
                    ],
                ],
            ],
        ];
    }
}
