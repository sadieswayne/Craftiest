<?php

declare(strict_types=1);

namespace App\Domains\Marketplace;

use App\Domains\Marketplace\Repositories\Contracts\ExtensionRepositoryInterface;
use App\Domains\Marketplace\Repositories\ExtensionRepository;
use App\Domains\Marketplace\Services\ExtensionInstallService;
use App\Domains\Marketplace\Services\ExtensionUninstallService;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class MarketplaceServiceProvider extends ServiceProvider
{
    /**
     * The service provider bindings.
     */
    public array $bindings = [
        ExtensionRepositoryInterface::class => ExtensionRepository::class,
    ];

    /**
     * The service provider bindings.
     *
     * @note Do not remove namespaces from the beginning of the class.
     */
    public static array $extensionProviders = [
        'chatbot'              => \App\Extensions\Chatbot\System\ChatbotServiceProvider::class,
        'focus-mode'           => \App\Extensions\FocusMode\System\FocusModeServiceProvider::class,
        'newsletter'           => \App\Extensions\Newsletter\System\NewsletterServiceProvider::class,
        'photo-studio'         => \App\Extensions\PhotoStudio\System\PhotoStudioServiceProvider::class,
        'ai-product-shot'      => \App\Extensions\ProductPhotography\System\ProductPhotographyServiceProvider::class,
        'ai-avatar'            => \App\Extensions\AiAvatar\System\AiAvatarServiceProvider::class,
        'ai-persona'           => \App\Extensions\AiPersona\System\AiPersonaServiceProvider::class,
        'ai-music'             => \App\Extensions\AiMusic\System\AiMusicServiceProvider::class,
        'ai-replica'           => \App\Extensions\AiReplica\System\AiReplicaServiceProvider::class,
        'ai-video-pro'         => \App\Extensions\AiVideoPro\System\AiVideoProServiceProvider::class,
        'seo-tool'             => \App\Extensions\SEOTool\System\SEOToolServiceProvider::class,
        'ai-social-media'      => \App\Extensions\AISocialMedia\System\AISocialMediaServiceProvider::class,
        'webchat'              => \App\Extensions\AIWebChat\System\AIWebChatServiceProvider::class,
        'onboarding'           => \App\Extensions\Onboarding\System\OnboardingServiceProvider::class,
        'flux-pro'             => \App\Extensions\FluxPro\System\FluxProServiceProvider::class,
        'chat-share'           => \App\Extensions\ChatShare\System\ChatShareServiceProvider::class,
        'voice-isolator'       => \App\Extensions\AIVoiceIsolator\System\AIVoiceIsolatorServiceProvider::class,
        'chat-setting'         => \App\Extensions\ChatSetting\System\ChatSettingServiceProvider::class,
        'hubspot'              => \App\Extensions\Hubspot\System\HubspotServiceProvider::class,
        'menu'                 => \App\Extensions\Menu\System\MenuServiceProvider::class,
        'azure-tts'            => \App\Extensions\AzureTTS\System\AzureTTSServiceProvider::class,
        'plagiarism'           => \App\Extensions\AIPlagiarism\System\AIPlagiarismServiceProvider::class,
        'cloudflare-r2'        => \App\Extensions\Cloudflare\System\CloudflareServiceProvider::class,
        'wordpress'            => \App\Extensions\Wordpress\System\WordpressServiceProvider::class,
        'cryptomus'            => \App\Extensions\Cryptomus\System\CryptomusServiceProvider::class,
        'affilate'             => \App\Extensions\Affilate\System\AffilateServiceProvider::class,
        'mailchimp-newsletter' => \App\Extensions\Mailchimp\System\MailchimpServiceProvider::class,
        'ai-writer-templates'  => \App\Extensions\AIWriterTemplates\System\AIWriterTemplateServiceProvider::class,
        'maintenance'          => \App\Extensions\Maintenance\System\MaintenanceServiceProvider::class,
        'open-router'          => \App\Extensions\OpenRouter\System\OpenRouterServiceProvider::class,
        'advanced-image'       => \App\Extensions\AdvancedImage\System\AdvancedImageServiceProvider::class,
        'mega-menu'            => \App\Extensions\MegaMenu\System\MegaMenuServiceProvider::class,
        'onboarding-pro'       => \App\Extensions\OnboardingPro\System\OnboardingProServiceProvider::class,
        'ideogram'             => \App\Extensions\Ideogram\System\IdeogramServiceProvider::class,
        'perplexity'       	   => \App\Extensions\Perplexity\System\PerplexityServiceProvider::class,
        'checkout-registration'=> \App\Extensions\CheckoutRegistration\System\RegistrationServiceProvider::class,
        'openai-realtime-chat' => \App\Extensions\OpenAIRealtimeChat\System\OpenAIRealtimeChatServiceProvider::class,
        'ai-video-to-video'    => \App\Extensions\AIVideoToVideo\System\AIVideoToVideoServiceProvider::class,
        'midjourney'           => \App\Extensions\Midjourney\System\MidjourneyServiceProvider::class,
        'social-media'         => \App\Extensions\SocialMedia\System\SocialMediaServiceProvider::class,
        'chatbot-agent'        => \App\Extensions\ChatbotAgent\System\ChatbotAgentServiceProvider::class,
        'xero'                 => \App\Extensions\Xero\System\XeroServiceProvider::class,
        'speechify-tts'        => \App\Extensions\SpeechifyTTS\System\SpeechifyServiceProvider::class,
        'anouncement'          => \App\Extensions\Anouncement\System\AnouncementServiceProvider::class,
    ];

    public function register(): void
    {
        $this->extensionProviderRegister();
    }

    public function boot(): void
    {
        $this->registerRoutes();
    }

    private function registerRoutes(): void
    {
        $this->router()
            ->group([
                'middleware' => ['web', 'auth'],
            ], function (Router $route) {
                $route->get('dashboard/marketplace/extension/{slug}/install', function (string $slug) {
                    return $this
                        ->app
                        ->make(ExtensionInstallService::class)
                        ->install($slug);
                })->name('marketplace.extension.install');

                $route->get('dashboard/marketplace/extension/{slug}/uninstall', function (string $slug) {
                    return $this
                        ->app
                        ->make(ExtensionUninstallService::class)
                        ->uninstall($slug);
                })->name('marketplace.extension.uninstall');
            });
    }

    private function router(): Router|Route
    {
        return $this->app['router'];
    }

    public function extensionProviderRegister(): void
    {
        foreach (static::$extensionProviders as $provider) {
            if (class_exists($provider)) {
                $this->app->register($provider);
            }
        }
    }

    public static function uninstallExtension(string $slug): void
    {
        if (isset(self::$extensionProviders[$slug])) {

            $provider = self::$extensionProviders[$slug];

            if (method_exists($provider, 'uninstall')) {
                $provider::uninstall();
            }
        }
    }

    public static function getExtensionProviders(): array
    {
        return static::$extensionProviders;
    }
}
