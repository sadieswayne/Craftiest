<?php

declare(strict_types=1);

namespace App\Domains\Engine\Enums;

use App\Domains\Engine\Drivers\AiMlMinimaxAIEngineDriver;
use App\Domains\Engine\Drivers\AnthropicEngineDriver;
use App\Domains\Engine\Drivers\AzureEngineDriver;
use App\Domains\Engine\Drivers\ClipDropEngineDriver;
use App\Domains\Engine\Drivers\DeepSeekAIEngineDriver;
use App\Domains\Engine\Drivers\ElevenlabsEngineDriver;
use App\Domains\Engine\Drivers\FallAIEngineDriver;
use App\Domains\Engine\Drivers\FreepikEngineDriver;
use App\Domains\Engine\Drivers\GeminiEngineDriver;
use App\Domains\Engine\Drivers\GoogleEngineDriver;
use App\Domains\Engine\Drivers\HeygenEngineDriver;
use App\Domains\Engine\Drivers\NovitaEngineDriver;
use App\Domains\Engine\Drivers\OpenAIEngineDriver;
use App\Domains\Engine\Drivers\OpenRouterEngineDriver;
use App\Domains\Engine\Drivers\PebblelyEngineDriver;
use App\Domains\Engine\Drivers\PerplexityEngineDriver;
use App\Domains\Engine\Drivers\PexelsEngineDriver;
use App\Domains\Engine\Drivers\PiAPIEngineDriver;
use App\Domains\Engine\Drivers\PixabayEngineDriver;
use App\Domains\Engine\Drivers\PlagiarismCheckEngineDriver;
use App\Domains\Engine\Drivers\SerperEngineDriver;
use App\Domains\Engine\Drivers\SpeechifyEngineDriver;
use App\Domains\Engine\Drivers\StableDiffusionEngineDriver;
use App\Domains\Engine\Drivers\SynthesiaEngineDriver;
use App\Domains\Engine\Drivers\UnsplashEngineDriver;
use App\Domains\Engine\Drivers\XAIEngineDriver;
use App\Domains\Entity\Enums\EntityEnum;
use App\Domains\Entity\Models\Entity;
use App\Enums\Contracts;
use App\Enums\Traits\EnumTo;
use App\Enums\Traits\SluggableEnumTrait;
use App\Enums\Traits\StringBackedEnumTrait;
use App\Models\Setting;
use App\Models\SettingTwo;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

enum EngineEnum: string implements Contracts\WithStringBackedEnum
{
    use EnumTo;
    use SluggableEnumTrait;
    use StringBackedEnumTrait;

    case OPEN_AI = 'openai';

    case PI_API = 'piapi';

    case DEEP_SEEK = 'deep_seek';

    case STABLE_DIFFUSION = 'stable_diffusion';

    case ANTHROPIC = 'anthropic';

    case GEMINI = 'gemini';

    case UNSPLASH = 'unsplash';

    case PEXELS = 'pexels';

    case PIXABAY = 'pixabay';

    case ELEVENLABS = 'elevenlabs';

    case GOOGLE = 'google';

    case AZURE = 'azure';

    case Speechify = 'speechify';

    case SERPER = 'serper';
    case PERPLEXITY = 'perplexity';

    case CLIPDROP = 'clipdrop';

    case NOVITA = 'novita';

    case FREEPIK = 'freepik';

    case PLAGIARISM_CHECK = 'plagiarism_check';

    case SYNTHESIA = 'synthesia';
    case HEYGEN = 'heygen';

    case PEBBLELY = 'pebblely';

    case FAL_AI = 'fal_ai';

    case X_AI = 'x_ai';

    case AI_ML_MINIMAX = 'minimax';

    case OPEN_ROUTER = 'open_router';

    public function label(): string
    {
        return match ($this) {
            self::OPEN_AI                => __('OpenAI'),
            self::DEEP_SEEK              => __('Deepseek'),
            self::STABLE_DIFFUSION       => __('Stable Diffusion'),
            self::ANTHROPIC              => __('Anthropic'),
            self::GEMINI                 => __('Gemini'),
            self::UNSPLASH               => __('Unsplash'),
            self::PEXELS                 => __('Pexels'),
            self::PIXABAY                => __('Pixabay'),
            self::ELEVENLABS             => __('Elevenlabs'),
            self::GOOGLE                 => __('Google TTS'),
            self::AZURE                  => __('Azure TTS'),
            self::Speechify              => __('Speechify TTS'),
            self::SERPER                 => __('Serper'),
            self::PERPLEXITY             => __('Perplexity'),
            self::CLIPDROP               => __('Clipdrop'),
            self::NOVITA                 => __('Novita'),
            self::FREEPIK                => __('Freepik'),
            self::PLAGIARISM_CHECK       => __('Plagiarism Check'),
            self::SYNTHESIA              => __('Synthesia'),
            self::HEYGEN                 => __('Heygen'),
            self::PEBBLELY               => __('Pebblely'),
            self::FAL_AI                 => __('Fal AI'),
            self::X_AI                   => __('X AI'),
            self::PI_API                 => __('PiAPI'),
            self::AI_ML_MINIMAX          => __('AI/ML Minimax'),
            self::OPEN_ROUTER            => __('Open Router'),
        };
    }

    public function driverClass(): string
    {
        return match ($this) {
            self::OPEN_AI          => OpenAIEngineDriver::class,
            self::PI_API           => PiAPIEngineDriver::class,
            self::DEEP_SEEK        => DeepSeekAIEngineDriver::class,
            self::STABLE_DIFFUSION => StableDiffusionEngineDriver::class,
            self::ANTHROPIC        => AnthropicEngineDriver::class,
            self::GEMINI           => GeminiEngineDriver::class,
            self::UNSPLASH         => UnsplashEngineDriver::class,
            self::PEXELS           => PexelsEngineDriver::class,
            self::PIXABAY          => PixabayEngineDriver::class,
            self::ELEVENLABS       => ElevenlabsEngineDriver::class,
            self::GOOGLE           => GoogleEngineDriver::class,
            self::AZURE            => AzureEngineDriver::class,
            self::Speechify        => SpeechifyEngineDriver::class,
            self::SERPER           => SerperEngineDriver::class,
            self::PERPLEXITY       => PerplexityEngineDriver::class,
            self::CLIPDROP         => ClipDropEngineDriver::class,
            self::NOVITA           => NovitaEngineDriver::class,
            self::FREEPIK          => FreepikEngineDriver::class,
            self::PLAGIARISM_CHECK => PlagiarismCheckEngineDriver::class,
            self::SYNTHESIA        => SynthesiaEngineDriver::class,
            self::HEYGEN           => HeygenEngineDriver::class,
            self::PEBBLELY         => PebblelyEngineDriver::class,
            self::FAL_AI           => FallAIEngineDriver::class,
            self::X_AI             => XAIEngineDriver::class,
            self::AI_ML_MINIMAX    => AiMlMinimaxAIEngineDriver::class,
            self::OPEN_ROUTER      => OpenRouterEngineDriver::class,
        };
    }

    public function models(): array
    {
        return collect(EntityEnum::cases())->filter(fn ($model) => $model->engine() === $this)->toArray();
    }

    /**
     * @return Collection<Entity>
     */
    public function getModels(): Collection
    {
        return Entity::byEngine($this)->get();
    }

    /**
     * @return Collection<Entity>
     */
    public function getEnabledModels(): Collection
    {
        return Cache::remember('engine_models_' . $this->value, now()->addMinutes(5), function () {
            return Entity::query()->isEnabled()->byEngine($this)->get();
        });
    }

    private function getDefaultOpenAiImageModel($settings_two): string
    {
        return match ($settings_two?->dalle) {
            'dalle3' => EntityEnum::DALL_E_3->slug(),
            'dalle2' => EntityEnum::DALL_E_2->slug(),
            default  => $settings_two?->dalle ?? 'dall-e-2',
        };
    }

    /**
     * @throws Exception
     */
    public function getDefaultModels(?Setting $setting, ?SettingTwo $settingTwo): array
    {
        return match ($this) {
            self::OPEN_AI          => [
                EntityEnum::fromSlug($setting?->openai_default_model ?? EntityEnum::GPT_4_O->slug()),
                EntityEnum::fromSlug($this->getDefaultOpenAiImageModel($settingTwo)),
                EntityEnum::TTS_1_HD,
                EntityEnum::TTS_1,
                EntityEnum::WHISPER_1,
                EntityEnum::TEXT_EMBEDDING_3_SMALL,
                ...(EntityEnum::fromSlug($setting?->openai_default_model ?? EntityEnum::GPT_4_O->slug()) !== EntityEnum::GPT_4_O
                    ? [EntityEnum::GPT_4_O]
                    : []),
                EntityEnum::GPT_4_O_REALTIME_PREVIEW,
                EntityEnum::GPT_4_O_SEARCH_PREVIEW,
                EntityEnum::GPT_4_O_MINI_SEARCH_PREVIEW,
            ],
            self::STABLE_DIFFUSION => [
                EntityEnum::fromSlug($settingTwo?->stable_diffusion_default_model ?? $settingTwo?->stablediffusion_default_model ?? EntityEnum::STABLE_DIFFUSION_XL_1024_V_1_0->slug()),
                EntityEnum::IMAGE_TO_VIDEO,
            ],
            self::ANTHROPIC        => [EntityEnum::fromSlug(setting('anthropic_default_model', EntityEnum::CLAUDE_3_OPUS->slug()))],
            self::OPEN_ROUTER      => [EntityEnum::fromSlug(setting('default_open_router_model', EntityEnum::PERPLEXITY_LLAMA_31_SONAR_8B->slug()))],
            self::GEMINI           => [EntityEnum::fromSlug(setting('gemini_default_model', EntityEnum::GEMINI_1_5_PRO_LATEST->slug()))],
            self::DEEP_SEEK        => [EntityEnum::fromSlug(setting('deepseek_default_model', EntityEnum::DEEPSEEK_CHAT->slug()))],
            self::ELEVENLABS       => [EntityEnum::ELEVENLABS, EntityEnum::ISOLATOR],
            self::PI_API 		        => [EntityEnum::MIDJOURNEY],
            self::FAL_AI           => [
                EntityEnum::fromSlug(setting('fal_ai_default_model', EntityEnum::FLUX_PRO->slug())),
                EntityEnum::VEO_2,
                EntityEnum::KLING,
                EntityEnum::LUMA_DREAM_MACHINE,
                EntityEnum::HAIPER,
                EntityEnum::MINIMAX,
                EntityEnum::IDEOGRAM,
                EntityEnum::VIDEO_UPSCALER,
                EntityEnum::COGVIDEOX_5B,
                EntityEnum::ANIMATEDIFF_V2V,
                EntityEnum::FAST_ANIMATEDIFF_TURBO,
            ],
            self::X_AI => [
                EntityEnum::GROK_2_1212,
                EntityEnum::GROK_2_VISION_1212,
            ],
            self::AI_ML_MINIMAX     => [EntityEnum::MUSIC_01],
            self::UNSPLASH          => [EntityEnum::UNSPLASH],
            self::PEXELS            => [EntityEnum::PEXELS],
            self::PIXABAY           => [EntityEnum::PIXABAY],
            self::GOOGLE            => [EntityEnum::GOOGLE],
            self::AZURE             => [EntityEnum::AZURE],
            self::Speechify         => [EntityEnum::Speechify],
            self::SERPER            => [EntityEnum::SERPER],
            self::PERPLEXITY        => [EntityEnum::PERPLEXITY],
            self::CLIPDROP          => [EntityEnum::CLIPDROP],
            self::NOVITA            => [EntityEnum::NOVITA],
            self::FREEPIK           => [EntityEnum::FREEPIK],
            self::PLAGIARISM_CHECK  => [EntityEnum::PLAGIARISMCHECK],
            self::SYNTHESIA         => [EntityEnum::SYNTHESIA],
            self::HEYGEN            => [EntityEnum::HEYGEN],
            self::PEBBLELY          => [EntityEnum::PEBBLELY],
            default                 => throw new Exception('No default model found for engine ' . $this->value),
        };
    }

    public function getDefaultWordModel(?Setting $setting): EntityEnum
    {
        return match ($this) {
            self::OPEN_AI          => EntityEnum::fromSlug($setting?->openai_default_model ?? EntityEnum::GPT_4_O->slug()),
            self::ANTHROPIC        => EntityEnum::fromSlug(setting('anthropic_default_model', EntityEnum::CLAUDE_3_OPUS->slug())),
            self::GEMINI           => EntityEnum::fromSlug(setting('gemini_default_model', EntityEnum::GEMINI_1_5_PRO_LATEST->slug())),
            self::DEEP_SEEK        => EntityEnum::fromSlug(setting('deepseek_default_model', EntityEnum::DEEPSEEK_CHAT->slug())),
            self::X_AI             => EntityEnum::fromSlug(setting('xai_default_model', EntityEnum::GROK_2_1212->slug())),
            default                => throw new Exception('No default model found for engine ' . $this->value),
        };
    }

    public function getDefaultImageModel(): ?EntityEnum
    {
        $settingTwo = SettingTwo::getCache();

        return match ($this) {
            self::OPEN_AI          => EntityEnum::fromSlug($this->getDefaultOpenAiImageModel($settingTwo)),
            self::STABLE_DIFFUSION => EntityEnum::fromSlug($settingTwo?->stable_diffusion_default_model ?? $settingTwo?->stablediffusion_default_model ?? EntityEnum::STABLE_DIFFUSION_XL_1024_V_1_0->slug()),
            self::FAL_AI           => EntityEnum::fromSlug(setting('fal_ai_default_model', EntityEnum::FLUX_PRO->slug())),
            self::PI_API 		        => EntityEnum::MIDJOURNEY,
            default                => null,
        };
    }

    public function getDefaultAWImageModel(?SettingTwo $settingTwo): EntityEnum
    {
        return match ($this) {
            self::OPEN_AI          => EntityEnum::fromSlug($this->getDefaultOpenAiImageModel($settingTwo)),
            self::STABLE_DIFFUSION => EntityEnum::fromSlug($settingTwo?->stable_diffusion_default_model ?? $settingTwo?->stablediffusion_default_model ?? EntityEnum::STABLE_DIFFUSION_XL_1024_V_1_0->slug()),
            self::UNSPLASH         => EntityEnum::UNSPLASH,
            self::PEXELS           => EntityEnum::PEXELS,
            self::PIXABAY          => EntityEnum::PIXABAY,
            default                => throw new Exception('No default model found for engine ' . $this->value),
        };
    }

    /**
     * @throws Exception
     */
    public function defaultEntitiesCount(): int
    {
        return count($this->getDefaultModels(Setting::getCache(), SettingTwo::getCache()));
    }

    /**
     * @throws Exception
     */
    public function getListableActiveModels(Setting $setting, SettingTwo $settingTwo): Collection
    {
        $defaultModelKeys = collect($this->getDefaultModels($setting, $settingTwo))->map(fn ($model) => $model->slug());

        // return all engine models without default models
        return $this->getEnabledModels()
            ->filter(fn (Entity $model) => EntityEnum::listableCases()->contains($model->key))
            ->filter(fn (Entity $model) => ! $defaultModelKeys->contains($model->key->slug()));
    }

    public static function whereHasEnabledModels(): array
    {
        return collect(self::cases())->filter(fn (EngineEnum $engine) => $engine->getEnabledModels()->isNotEmpty())->toArray();
    }

    public static function getNestedPlanLimits(): array
    {
        return collect(self::cases())->mapWithKeys(function (EngineEnum $engine) {
            return [$engine->slug() => EntityEnum::getPlanLimits($engine)->toArray()];
        })->toArray();
    }

    public static function rules(string $prefix = '', array $rules = []): array
    {
        return collect(self::cases())
            ->mapWithKeys(function (EngineEnum $engine) use ($prefix, $rules) {
                return [$prefix . $engine->slug() => collect($engine->models())->mapWithKeys(function (EntityEnum $model) use ($rules) {
                    return [$model->slug() => [
                        'credit'      => $rules[0],
                        'isUnlimited' => $rules[1],
                    ]];
                })->toArray()];
            })->dot()->toArray();
    }
}
