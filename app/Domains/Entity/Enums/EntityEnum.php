<?php

declare(strict_types=1);

namespace App\Domains\Entity\Enums;

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Entity\Contracts\Calculate\WithCharsInterface;
use App\Domains\Entity\Contracts\Calculate\WithImagesInterface;
use App\Domains\Entity\Contracts\Calculate\WithImageToVideoInterface;
use App\Domains\Entity\Contracts\Calculate\WithPlagiarismInterface;
use App\Domains\Entity\Contracts\Calculate\WithSpeechToTextInterface;
use App\Domains\Entity\Contracts\Calculate\WithTextToSpeechInterface;
use App\Domains\Entity\Contracts\Calculate\WithTextToVideoInterface;
use App\Domains\Entity\Contracts\Calculate\WithVideoToVideoInterface;
use App\Domains\Entity\Contracts\Calculate\WithVisionPreviewInterface;
use App\Domains\Entity\Contracts\Calculate\WithWordsInterface;
use App\Domains\Entity\Drivers\AiMlMinimax;
use App\Domains\Entity\Drivers\Anthropic;
use App\Domains\Entity\Drivers\AzureDriver;
use App\Domains\Entity\Drivers\ClipDropDriver;
use App\Domains\Entity\Drivers\Deepseek;
use App\Domains\Entity\Drivers\ElevenLabs;
use App\Domains\Entity\Drivers\FalAI;
use App\Domains\Entity\Drivers\FreepikDriver;
use App\Domains\Entity\Drivers\Gemini;
use App\Domains\Entity\Drivers\GoogleDriver;
use App\Domains\Entity\Drivers\HeygenDriver;
use App\Domains\Entity\Drivers\NovitaDriver;
use App\Domains\Entity\Drivers\OpenAI;
use App\Domains\Entity\Drivers\OpenRouter;
use App\Domains\Entity\Drivers\PebblelyDriver;
use App\Domains\Entity\Drivers\PerplexityDriver;
use App\Domains\Entity\Drivers\PexelsDriver;
use App\Domains\Entity\Drivers\PiAPI\MidjourneyDriver;
use App\Domains\Entity\Drivers\PixabayDriver;
use App\Domains\Entity\Drivers\PlagiarismCheckDriver;
use App\Domains\Entity\Drivers\SerperDriver;
use App\Domains\Entity\Drivers\SpeechifyDriver;
use App\Domains\Entity\Drivers\StableDiffusion;
use App\Domains\Entity\Drivers\SynthesiaDriver;
use App\Domains\Entity\Drivers\UnsplashDriver;
use App\Domains\Entity\Drivers\XAI;
use App\Enums\AITokenType;
use App\Enums\Traits\EnumTo;
use App\Enums\Traits\SluggableEnumTrait;
use App\Enums\Traits\StringBackedEnumTrait;
use Illuminate\Support\Collection;

enum EntityEnum: string
{
    use EnumTo;
    use SluggableEnumTrait;
    use StringBackedEnumTrait;

    // Anthropic
    case CLAUDE_3_7_SONNET = 'claude-3-7-sonnet-20250219';
    case CLAUDE_3_5_HAIKU = 'claude-3-haiku-20241022';
    case CLAUDE_3_5_SONNET_V2 = 'claude-3-5-sonnet-20241022';
    case CLAUDE_3_5_SONNET = 'claude-3-5-sonnet-20240620';
    case CLAUDE_3_SONNET = 'claude-3-sonnet-20240229';
    case CLAUDE_3_OPUS = 'claude-3-opus-20240229';
    case CLAUDE_3_HAIKU = 'claude-3-haiku-20240307';
    case CLAUDE_2_1 = 'claude-2.1';
    case CLAUDE_2_0 = 'claude-2.0';

    // Embeding models for Anthropic
    case VOYAGE_2 = 'voyage-2';

    case VOYAGE_LARGE_2 = 'voyage-large-2';

    case VOYAGE_CODE_2 = 'voyage-code-2';
    // OpenAI
    case DAVINCI = 'davinci-002';

    case TEXT_DAVINCI_003 = 'text-davinci-003';

    case GPT_3_5_TURBO_16K = 'gpt-3.5-turbo-16k';

    case GPT_3_5_TURBO = 'gpt-3.5-turbo';

    case GPT_3_5_TURBO_0125 = 'gpt-3.5-turbo-0125';

    case GPT_3_5_TURBO_1106 = 'gpt-3.5-turbo-1106';

    case GPT_4 = 'gpt-4';

    case GPT_4_TURBO = 'gpt-4-turbo';

    case GPT_4_1106_PREVIEW = 'gpt-4-1106-preview';

    case GPT_4_0125_PREVIEW = 'gpt-4-0125-preview';

    case GPT_4_VISION_PREVIEW = 'gpt-4-vision-preview';

    case GPT_4_O = 'gpt-4o';

    case GPT_4_O_MINI = 'gpt-4o-mini';

    case GPT_4_O_SEARCH_PREVIEW = 'gpt-4o-search-preview';
    case GPT_4_O_MINI_SEARCH_PREVIEW = 'gpt-4o-mini-search-preview';

    case GPT_4_O1_PREVIEW = 'o1-preview';

    case GPT_4_O1_MINI = 'o1-mini';

    case GPT_O_03_mini = 'o3-mini';

    case GPT_4_5_PREVIEW = 'gpt-4.5-preview';

    case GPT_4_O_REALTIME_PREVIEW = 'gpt-4o-realtime-preview-2024-12-17';

    // embeding models for openai
    case TEXT_EMBEDDING_ADA_002 = 'text-embedding-ada-002';

    case TEXT_EMBEDDING_3_SMALL = 'text-embedding-3-small';

    case TEXT_EMBEDDING_3_LARGE = 'text-embedding-3-large';

    // Stable Diffusion
    case IMAGE_TO_VIDEO = 'image-to-video';

    case STABLE_DIFFUSION_XL_1024_V_1_0 = 'stable-diffusion-xl-1024-v1-0';

    case STABLE_DIFFUSION_V_1_6 = 'stable-diffusion-v1-6';

    case SD_3 = 'sd3';

    case SD_3_TURBO = 'sd3-turbo';

    case SD_3_MEDIUM = 'sd3-medium';

    case SD_3_LARGE = 'sd3-large';

    case SD_3_LARGE_TURBO = 'sd3-large-turbo';

    case SD_3_5_LARGE = 'sd3.5-large';

    case SD_3_5_LARGE_TURBO = 'sd3.5-large-turbo';

    case SD_3_5_MEDIUM = 'sd3.5-medium';

    case CORE = 'core';

    case ULTRA = 'ultra';

    case AWS_BEDROCK = 'aws_bedrock';

    case CLIPDROP = 'clipdrop';

    case NOVITA = 'novita';

    case FREEPIK = 'freepik';

    case PLAGIARISMCHECK = 'plagiarismcheck';

    case SYNTHESIA = 'synthesia';

    case HEYGEN = 'heygen';

    case PEBBLELY = 'pebblely';

    case DEEPSEEK_CHAT = 'deepseek-chat';

    case GEMINI_TEXT_EMBEDING_004 = 'text-embedding-004';

    case GEMINI_1_5_PRO_LATEST = 'gemini-1.5-pro-latest';

    case DEEPSEEK_REASONER = 'deepseek-reasoner';

    case GEMINI_PRO = 'gemini-pro';

    case GEMINI_1_5_FLASH = 'gemini-1.5-flash';

    case GEMINI_PRO_VISION = 'gemini-pro-vision';

    case UNSPLASH = 'unsplash';

    case PEXELS = 'pexels';

    case PIXABAY = 'pixabay';

    case ELEVENLABS = 'elevenlabs';

    case ISOLATOR = 'isolator';

    case GOOGLE = 'google';

    case AZURE = 'azure';

    case Speechify = 'speechify';

    case SERPER = 'serper';

    case PERPLEXITY = 'perplexity';

    case WHISPER_1 = 'whisper-1';

    case DALL_E_2 = 'dall-e-2';

    case DALL_E_3 = 'dall-e-3';
    case TTS_1 = 'tts-1';
    case TTS_1_HD = 'tts-1-hd';
    case GROK_2_1212 = 'grok-2-1212';
    case GROK_2_VISION_1212 = 'grok-2-vision-1212';
    case VEO_2 = 'veo2';

    case FLUX_PRO = 'flux-pro';

    case IDEOGRAM = 'ideogram-v2';

    case FLUX_PRO_1_1 = 'flux-pro/v1.1';

    case FLUX_REALISM = 'flux-realism';

    case FLUX_SCHNELL = 'flux/schnell';

    case KLING = 'kling';

    case LUMA_DREAM_MACHINE = 'luma-dream-machine';

    case HAIPER = 'haiper';

    case MINIMAX = 'minimax';

    case MUSIC_01 = 'music-01';
    // Open router
    case ANTHROPIC_CLAUDE_3_5_HAIKU_20241022 = 'anthropic/claude-3-5-haiku-20241022';
    case ANTHROPIC_CLAUDE_3_5_HAIKU_20241022_SELF_MODERATED = 'anthropic/claude-3-5-haiku-20241022:beta';
    case ANTHROPIC_CLAUDE_3_5_HAIKU = 'anthropic/claude-3-5-haiku';
    case ANTHROPIC_CLAUDE_3_5_HAIKU_SELF_MODERATED = 'anthropic/claude-3-5-haiku:beta';
    case LUMIMAID_V02_70B = 'neversleep/llama-3.1-lumimaid-70b';
    case MAGNUM_V4_72B = 'anthracite-org/magnum-v4-72b';
    case XAI_GROK_BETA = 'x-ai/grok-beta';
    case MINISTRAL_8B = 'mistralai/ministral-8b';
    case MINISTRAL_3B = 'mistralai/ministral-3b';
    case QWEN25_7B_INSTRUCT = 'qwen/qwen-2.5-7b-instruct';
    case NVIDIA_LLAMA_31_NEMOTRON_70B_INSTRUCT = 'nvidia/llama-3.1-nemotron-70b-instruct';
    case INFLECTION_3_PI = 'inflection/inflection-3-pi';
    case INFLECTION_3_PRODUCTIVITY = 'inflection/inflection-3-productivity';
    case LIQUID_LFM_40B_MOE_FREE = 'liquid/lfm-40b:free';
    case LIQUID_LFM_40B_MOB = 'liquid/lfm-40b';
    case ROCINANTE_12B = 'thedrummer/rocinante-12b';
    case EVA_QWEN25_14B = 'eva-unit-01/eva-qwen-2.5-14b';
    case MAGNUM_V2_72B = 'anthracite-org/magnum-v2-72b';
    case META_LLAMA32_3B_INSTRUCT_FREE = 'meta-llama/llama-3.2-3b-instruct:free';
    case META_LLAMA32_1B_INSTRUCT_FREE = 'meta-llama/llama-3.2-1b-instruct:free';
    case META_LLAMA32_3B_INSTRUCT = 'meta-llama/llama-3.2-3b-instruct';
    case META_LLAMA32_1B_INSTRUCT = 'meta-llama/llama-3.2-1b-instruct';
    case PERPLEXITY_LLAMA_31_SONAR_405B_ONLINE = 'perplexity/llama-3.1-sonar-huge-128k-online';
    case PERPLEXITY_LLAMA_31_SONAR_70B_ONLINE = 'perplexity/llama-3.1-sonar-large-128k-online';
    case PERPLEXITY_LLAMA_31_SONAR_70B = 'perplexity/llama-3.1-sonar-large-128k-chat';
    case PERPLEXITY_LLAMA_31_SONAR_8B_ONLINE = 'perplexity/llama-3.1-sonar-small-128k-online';
    case PERPLEXITY_LLAMA_31_SONAR_8B = 'perplexity/llama-3.1-sonar-small-128k-chat';

    case MIDJOURNEY = 'midjourney';

    // Fall AI video to video
    case VIDEO_UPSCALER = 'video-upscaler';

    case COGVIDEOX_5B = 'cogvideox-5b/video-to-video';

    case ANIMATEDIFF_V2V = 'animatediff-v2v';

    case FAST_ANIMATEDIFF_TURBO = 'fast-animatediff/turbo/video-to-video';

    public static function listableCases(): Collection
    {
        return collect(self::cases())->map(
            fn ($case) => $case->creditBy()
        )->unique()->values();
    }

    public function creditBy(): self
    {
        // we are not using this for now, because we only show default model for user
        return match ($this) {
            // self::GPT_3_5_TURBO_0125,
            // self::GPT_3_5_TURBO_1106,
            // self::GPT_3_5_TURBO_16K  => self::GPT_3_5_TURBO,
            // self::GPT_4_0125_PREVIEW,
            // self::GPT_4_1106_PREVIEW => self::GPT_4_VISION_PREVIEW,
            default                  => $this
        };
    }

    public function creditIndex(): float
    {
        return 1.0;
    }

    public function label(): string
    {
        return match ($this) {
            self::IMAGE_TO_VIDEO                    => __('AI Video'),
            self::STABLE_DIFFUSION_XL_1024_V_1_0    => __('Stable Diffusion XL 1.0'),
            self::STABLE_DIFFUSION_V_1_6            => __('Stable Diffusion 1.6'),
            self::SD_3                              => __('Stable Diffusion 3'),
            self::SD_3_TURBO                        => __('Stable Diffusion 3 turbo'),
            self::SD_3_MEDIUM                       => __('Stable Diffusion 3 Medium'),
            self::SD_3_LARGE                        => __('Stable Diffusion 3 Large'),
            self::SD_3_LARGE_TURBO                  => __('Stable Diffusion 3 Large Turbo'),
            self::SD_3_5_LARGE                      => __('Stable Diffusion 3.5 Large'),
            self::SD_3_5_LARGE_TURBO                => __('Stable Diffusion 3.5 Large Turbo'),
            self::SD_3_5_MEDIUM                     => __('Stable Diffusion 3.5 Medium'),
            self::CORE                              => __('Core'),
            self::ULTRA                             => __('Ultra'),
            self::AWS_BEDROCK                       => __('AWS Bedrock'),
            // OpenAI
            self::DAVINCI                     => __('Davinci 002 (Expensive &amp; Capable)'),
            self::TEXT_DAVINCI_003            => __('Davinci 003 (Expensive &amp; Capable)'),
            self::GPT_3_5_TURBO_16K           => __('GTP (3.5-turbo-16k)'),
            self::GPT_3_5_TURBO               => __('GPT 3.5-turbo (Most Expensive & Fastest & Most Capable)'),
            self::GPT_3_5_TURBO_0125          => __('GTP 3.5-turbo-0125 (Updated Knowleddge cutoff of Sep 2021, 16k)'),
            self::GPT_3_5_TURBO_1106          => __('GTP 3.5-turbo-1106 (Updated Knowleddge cutoff of Nov 2021, 16k)'),
            self::GPT_4_TURBO                 => __('GPT-4 Turbo (Most Expensive & Fastest & Most Capable)'),
            self::GPT_4                       => __('GPT-4 (Most Expensive & Fastest & Most Capable)'),
            self::GPT_4_1106_PREVIEW          => __('GPT-4-1106 Turbo (Updated Knowleddge cutoff of April 2023, 128k)'),
            self::GPT_4_0125_PREVIEW          => __('GPT-4-0125 Turbo (Updated Knowleddge cutoff of Dec 2023, 128k)'),
            self::GPT_4_VISION_PREVIEW        => __('GPT-4 Turbo with vision (Understand images, in addition to all other GPT-4 Turbo capabilites)'),
            self::TEXT_EMBEDDING_ADA_002      => __('Text Embedding Ada (Expensive &amp; Capable)'),
            self::TEXT_EMBEDDING_3_SMALL      => __('Text Embedding Small'),
            self::TEXT_EMBEDDING_3_LARGE      => __('Text Embedding Large'),
            self::WHISPER_1                   => __('WHISPER 1 The latest text to speech model, optimized for speed.'),
            self::DALL_E_2                    => __('DALL-E 2 The previous DALL·E model released in Nov 2022.'),
            self::DALL_E_3                    => __('DALL-E 3 The latest DALL·E model released in Nov 2023.'),
            self::TTS_1                       => __('TTS 1 The latest text to speech model, optimized for speed.'),
            self::TTS_1_HD                    => __('TTS 1 HD The latest text to speech model, optimized for quality.'),
            self::GPT_4_O                     => __('GPT-4o Most advanced works for Vision, multimodal flagship model that’s cheaper and faster than GPT-4 Turbo.  (Updated Knowleddge cutoff of Oct 2023, 128k)'),
            self::GPT_4_O_MINI                => __('GPT-4o mini Our affordable and intelligent small model for fast, lightweight tasks. GPT-4o mini is cheaper and more capable than GPT-3.5 Turbo.'),
            self::GPT_4_O_SEARCH_PREVIEW      => __('GPT-4o Search Preview'),
            self::GPT_4_O_MINI_SEARCH_PREVIEW => __('GPT-4o Mini Search Preview'),
            self::GPT_4_O1_PREVIEW            => __('GPT o1-preview (Updated Knowledge cutoff of Dec 2023, 128k)'),
            self::GPT_4_O1_MINI               => __('GPT o1-mini (Updated Knowledge cutoff of Dec 2023, 128k)'),
            self::GPT_O_03_mini               => __('GPT o3-mini (Updated Knowledge cutoff of October 2023, 200k)'),
            self::GPT_4_5_PREVIEW             => __('GPT-4.5-preview (Updated Knowledge cutoff of October 2023, 128k)'),
            self::GPT_4_O_REALTIME_PREVIEW    => __('GPT-4o Realtime Preview (Updated Knowledge cutoff of December 2024, 128k)'),
            // Anthropic
            self::CLAUDE_3_7_SONNET        => __('Claude 3.7 Sonnet'),
            self::CLAUDE_3_5_HAIKU         => __('Claude 3.5 Haiku'),
            self::CLAUDE_3_5_SONNET_V2     => __('Claude 3.5 Sonnet V2'),
            self::CLAUDE_3_5_SONNET        => __('Claude 3.5 Sonnet'),
            self::CLAUDE_3_SONNET          => __('Claude 3 Sonnet'),
            self::CLAUDE_3_OPUS            => __('Claude 3 Opus'),
            self::CLAUDE_3_HAIKU           => __('Claude 3 Haiku'),
            self::CLAUDE_2_1               => __('Claude 2.1'),
            self::CLAUDE_2_0               => __('Claude 2'),
            self::VOYAGE_2                 => __('Voyage 2'),
            self::VOYAGE_LARGE_2           => __('Voyage Large 2'),
            self::VOYAGE_CODE_2            => __('Voyage Code 2'),
            self::CLIPDROP                 => __('Clipdrop for Photo Studio'),
            self::NOVITA                   => __('Novita for Photo Studio'),
            self::FREEPIK                  => __('Novita for Image Editor'),
            self::PLAGIARISMCHECK          => __('Plagiarism Check'),
            self::SYNTHESIA                => __('Synthesia'),
            self::HEYGEN                   => __('Heygen'),
            self::PEBBLELY                 => __('Pebblely'),
            self::GEMINI_TEXT_EMBEDING_004 => __('Gemini Text Embeding 004'),
            self::GEMINI_1_5_PRO_LATEST    => __('Gemini 1.5 Pro (Preview only) (Model last updated: April 2024)'),
            self::GEMINI_PRO               => __('Gemini 1.0 Pro (Model last updated: February 2024)'),
            self::GEMINI_1_5_FLASH         => __('Gemini 1.0 Pro Vision (Model last updated: February 2023)'),
            self::GEMINI_PRO_VISION        => __('Gemini Pro Vision'),
            // Deepseek
            self::DEEPSEEK_CHAT            => __('Deepseek Chat'),
            self::DEEPSEEK_REASONER        => __('Deepseek DeepSeek-R1'),
            // Unsplash
            self::UNSPLASH => __('Unsplash for AI Article Wizard'),
            // Pexels
            self::PEXELS => __('Pexels for AI Article Wizard'),
            // Pixabay
            self::PIXABAY => __('Pixabay for AI Article Wizard'),
            // Elevenlabs
            self::ELEVENLABS => __('Elevenlabs for TTS'),
            self::ISOLATOR   => __('Voice Isolator (1 word = 5 used characters of elevenlabs) X 1 token'),
            // Google
            self::GOOGLE => __('Google for TTS'),
            // Azure
            self::AZURE => __('Azure for TTS'),
            // Speechify
            self::Speechify=> __('Speechify for TTS'),
            // Serper
            self::SERPER => __('Serper for Realtime Data'),
            // Perplexity
            self::PERPLEXITY => __('Perplexity for Realtime Data'),
            // Midjourney
            self::MIDJOURNEY => __('Midjourney'),
            // X AI
            self::GROK_2_1212        => __('Grok 2 1212'),
            self::GROK_2_VISION_1212 => __('Grok 2 Vision 1212'),
            // FAL AI
            self::VEO_2                    => __('Google VEO 2'),
            self::FLUX_PRO                 => __('Flux Pro'),
            self::IDEOGRAM                 => __('Ideogram V2'),
            self::FLUX_PRO_1_1             => __('Flux Pro 1.1'),
            self::FLUX_REALISM             => __('Flux Realism'),
            self::FLUX_SCHNELL             => __('Flux Schnell'),
            self::KLING                    => __('Kling 1.0'),
            self::LUMA_DREAM_MACHINE       => __('Luma Dream Machine'),
            self::HAIPER                   => __('Haiper'),
            self::MINIMAX                  => __('Minimax'),
            self::VIDEO_UPSCALER		         => __('Video Upscaler'),
            self::COGVIDEOX_5B			          => __('Cogvideox 5B'),
            self::ANIMATEDIFF_V2V		        => __('Animatediff V2V'),
            self::FAST_ANIMATEDIFF_TURBO   => __('Fast Animatediff Turbo'),
            // AI/ML api minimax engine
            self::MUSIC_01				 => __('Music 01'),
            // Open Router
            self::ANTHROPIC_CLAUDE_3_5_HAIKU_20241022                => __('Anthropic: Claude 3.5 Haiku (2024-10-22)'),
            self::ANTHROPIC_CLAUDE_3_5_HAIKU_20241022_SELF_MODERATED => __('Anthropic: Claude 3.5 Haiku (2024-10-22) (self-moderated)'),
            self::ANTHROPIC_CLAUDE_3_5_HAIKU                         => __('Anthropic: Claude 3.5 Haiku'),
            self::ANTHROPIC_CLAUDE_3_5_HAIKU_SELF_MODERATED          => __('Anthropic: Claude 3.5 Haiku (self-moderated)'),
            self::LUMIMAID_V02_70B                                   => __('Lumimaid v0.2 70B'),
            self::MAGNUM_V4_72B                                      => __('Magnum v4 72B'),
            self::XAI_GROK_BETA                                      => __('xAI: Grok Beta'),
            self::MINISTRAL_8B                                       => __('Ministral 8B'),
            self::MINISTRAL_3B                                       => __('Ministral 3B'),
            self::QWEN25_7B_INSTRUCT                                 => __('Qwen2.5 7B Instruct'),
            self::NVIDIA_LLAMA_31_NEMOTRON_70B_INSTRUCT              => __('NVIDIA: Llama 3.1 Nemotron 70B Instruct'),
            self::INFLECTION_3_PI                                    => __('Inflection: Inflection 3 Pi'),
            self::INFLECTION_3_PRODUCTIVITY                          => __('Inflection: Inflection 3 Productivity'),
            self::LIQUID_LFM_40B_MOE_FREE                            => __('Liquid: LFM 40B MoE (free)'),
            self::LIQUID_LFM_40B_MOB                                 => __('Liquid: LFM 40B MoE'),
            self::ROCINANTE_12B                                      => __('Rocinante 12B'),
            self::EVA_QWEN25_14B                                     => __('EVA Qwen2.5 14B'),
            self::MAGNUM_V2_72B                                      => __('Magnum v2 72B'),
            self::META_LLAMA32_3B_INSTRUCT_FREE                      => __('Meta: Llama 3.2 3B Instruct (free)'),
            self::META_LLAMA32_1B_INSTRUCT_FREE                      => __('Meta: Llama 3.2 1B Instruct (free)'),
            self::META_LLAMA32_3B_INSTRUCT                           => __('Meta: Llama 3.2 3B Instruct'),
            self::META_LLAMA32_1B_INSTRUCT                           => __('Meta: Llama 3.2 1B Instruct'),
            self::PERPLEXITY_LLAMA_31_SONAR_405B_ONLINE              => __('Perplexity: Llama 3.1 Sonar 405B Online'),
            self::PERPLEXITY_LLAMA_31_SONAR_70B_ONLINE               => __('Perplexity: Llama 3.1 Sonar 70B Online'),
            self::PERPLEXITY_LLAMA_31_SONAR_70B                      => __('Perplexity: Llama 3.1 Sonar 70B'),
            self::PERPLEXITY_LLAMA_31_SONAR_8B_ONLINE                => __('Perplexity: Llama 3.1 Sonar 8B Online'),
            self::PERPLEXITY_LLAMA_31_SONAR_8B                       => __('Perplexity: Llama 3.1 Sonar 8B'),
        };
    }

    public function isV2BetaSdEntity(): bool
    {
        return match ($this) {
            self::SD_3,
            self::SD_3_TURBO,
            self::SD_3_MEDIUM,
            self::SD_3_LARGE,
            self::SD_3_LARGE_TURBO,
            self::SD_3_5_LARGE,
            self::SD_3_5_LARGE_TURBO,
            self::SD_3_5_MEDIUM,
            self::CORE,
            self::ULTRA => true,
            default     => false,
        };
    }

    public function engine(): EngineEnum
    {
        return match ($this) {
            self::IMAGE_TO_VIDEO,
            self::STABLE_DIFFUSION_XL_1024_V_1_0,
            self::STABLE_DIFFUSION_V_1_6,
            self::SD_3,
            self::SD_3_TURBO,
            self::SD_3_MEDIUM,
            self::SD_3_LARGE,
            self::SD_3_LARGE_TURBO,
            self::SD_3_5_LARGE,
            self::SD_3_5_LARGE_TURBO,
            self::SD_3_5_MEDIUM,
            self::CORE,
            self::ULTRA,
            self::AWS_BEDROCK => EngineEnum::STABLE_DIFFUSION,
            // OpenAI
            self::DAVINCI,
            self::TEXT_DAVINCI_003,
            self::GPT_3_5_TURBO_16K,
            self::GPT_3_5_TURBO,
            self::GPT_3_5_TURBO_0125,
            self::GPT_3_5_TURBO_1106,
            self::GPT_4_TURBO,
            self::GPT_4,
            self::GPT_4_1106_PREVIEW,
            self::GPT_4_0125_PREVIEW,
            self::GPT_4_VISION_PREVIEW,
            self::TEXT_EMBEDDING_ADA_002,
            self::TEXT_EMBEDDING_3_SMALL,
            self::TEXT_EMBEDDING_3_LARGE,
            self::WHISPER_1,
            self::DALL_E_2,
            self::DALL_E_3,
            self::TTS_1,
            self::TTS_1_HD,
            self::GPT_4_O,
            self::GPT_4_O_MINI,
            self::GPT_4_O_SEARCH_PREVIEW,
            self::GPT_4_O_MINI_SEARCH_PREVIEW,
            self::GPT_4_O1_PREVIEW,
            self::GPT_4_O1_MINI,
            self::GPT_O_03_mini,
            self::GPT_4_5_PREVIEW,
            self::GPT_4_O_REALTIME_PREVIEW => EngineEnum::OPEN_AI,
            // Anthropic
            self::CLAUDE_3_7_SONNET,
            self::CLAUDE_3_5_HAIKU,
            self::CLAUDE_3_5_SONNET_V2,
            self::CLAUDE_3_5_SONNET,
            self::CLAUDE_3_SONNET,
            self::CLAUDE_3_OPUS,
            self::CLAUDE_3_HAIKU,
            self::CLAUDE_2_1,
            self::CLAUDE_2_0,
            self::VOYAGE_2,
            self::VOYAGE_LARGE_2,
            self::VOYAGE_CODE_2 => EngineEnum::ANTHROPIC,
            // Deepseek
            self::DEEPSEEK_CHAT, self::DEEPSEEK_REASONER => EngineEnum::DEEP_SEEK,
            // Clipdrop
            self::CLIPDROP => EngineEnum::CLIPDROP,
            // Novita
            self::NOVITA => EngineEnum::NOVITA,
            // Freepik
            self::FREEPIK => EngineEnum::FREEPIK,
            // Plagiarism Check
            self::PLAGIARISMCHECK => EngineEnum::PLAGIARISM_CHECK,
            // SYNTHESIA
            self::SYNTHESIA => EngineEnum::SYNTHESIA,
            // HEYGEN
            self::HEYGEN => EngineEnum::HEYGEN,
            // Pebblely
            self::PEBBLELY => EngineEnum::PEBBLELY,
            // Gemini
            self::GEMINI_1_5_PRO_LATEST,
            self::GEMINI_PRO,
            self::GEMINI_1_5_FLASH,
            self::GEMINI_PRO_VISION,
            self::GEMINI_TEXT_EMBEDING_004 => EngineEnum::GEMINI,
            // Unsplash
            self::UNSPLASH => EngineEnum::UNSPLASH,
            // Pexels
            self::PEXELS => EngineEnum::PEXELS,
            // Pixabay
            self::PIXABAY => EngineEnum::PIXABAY,
            // Elevenlabs
            self::ELEVENLABS,
            self::ISOLATOR => EngineEnum::ELEVENLABS,
            // Google
            self::GOOGLE => EngineEnum::GOOGLE,
            // Azure
            self::AZURE => EngineEnum::AZURE,
            // Speechify
            self::Speechify => EngineEnum::Speechify,
            // Serper
            self::SERPER => EngineEnum::SERPER,
            // Perplexity
            self::PERPLEXITY => EngineEnum::PERPLEXITY,
            // PIAPI
            self::MIDJOURNEY => EngineEnum::PI_API,
            // X AI
            self::GROK_2_1212, self::GROK_2_VISION_1212 => EngineEnum::X_AI,
            // FAL AI
            self::VEO_2, self::VIDEO_UPSCALER, self::COGVIDEOX_5B, self::ANIMATEDIFF_V2V, self::FAST_ANIMATEDIFF_TURBO, self::FLUX_PRO, self::FLUX_PRO_1_1, self::FLUX_REALISM, self::FLUX_SCHNELL, self::IDEOGRAM,
            self::KLING, self::LUMA_DREAM_MACHINE, self::HAIPER, self::MINIMAX => EngineEnum::FAL_AI,
            // AI/ML api minimax engine
            self::MUSIC_01 => EngineEnum::AI_ML_MINIMAX,
            // Open Router
            self::ANTHROPIC_CLAUDE_3_5_HAIKU_20241022,
            self::ANTHROPIC_CLAUDE_3_5_HAIKU_20241022_SELF_MODERATED,
            self::ANTHROPIC_CLAUDE_3_5_HAIKU,
            self::ANTHROPIC_CLAUDE_3_5_HAIKU_SELF_MODERATED,
            self::LUMIMAID_V02_70B,
            self::MAGNUM_V4_72B,
            self::XAI_GROK_BETA,
            self::MINISTRAL_8B,
            self::MINISTRAL_3B,
            self::QWEN25_7B_INSTRUCT,
            self::NVIDIA_LLAMA_31_NEMOTRON_70B_INSTRUCT,
            self::INFLECTION_3_PI,
            self::INFLECTION_3_PRODUCTIVITY,
            self::LIQUID_LFM_40B_MOE_FREE,
            self::LIQUID_LFM_40B_MOB,
            self::ROCINANTE_12B,
            self::EVA_QWEN25_14B,
            self::MAGNUM_V2_72B,
            self::META_LLAMA32_3B_INSTRUCT_FREE,
            self::META_LLAMA32_1B_INSTRUCT_FREE,
            self::META_LLAMA32_3B_INSTRUCT,
            self::META_LLAMA32_1B_INSTRUCT,
            self::PERPLEXITY_LLAMA_31_SONAR_405B_ONLINE,
            self::PERPLEXITY_LLAMA_31_SONAR_70B_ONLINE,
            self::PERPLEXITY_LLAMA_31_SONAR_70B,
            self::PERPLEXITY_LLAMA_31_SONAR_8B_ONLINE,
            self::PERPLEXITY_LLAMA_31_SONAR_8B  => EngineEnum::OPEN_ROUTER,
        };
    }

    public function driverClass(): string
    {
        return match ($this) {
            // Stable Diffusion
            self::IMAGE_TO_VIDEO                   => StableDiffusion\ImageToVideoDriver::class,
            self::STABLE_DIFFUSION_XL_1024_V_1_0   => StableDiffusion\XL1024V10Driver::class,
            self::STABLE_DIFFUSION_V_1_6           => StableDiffusion\V16Driver::class,
            self::SD_3                             => StableDiffusion\Sd3Driver::class,
            self::SD_3_TURBO                       => StableDiffusion\Sd3TurboDriver::class,
            self::SD_3_MEDIUM                      => StableDiffusion\Sd3MediumDriver::class,
            self::SD_3_LARGE                       => StableDiffusion\Sd3LargeDriver::class,
            self::SD_3_LARGE_TURBO                 => StableDiffusion\Sd3LargeTurboDriver::class,
            self::SD_3_5_LARGE                     => StableDiffusion\Sd35LargeDriver::class,
            self::SD_3_5_LARGE_TURBO               => StableDiffusion\Sd35LargeTurboDriver::class,
            self::SD_3_5_MEDIUM                    => StableDiffusion\Sd35MediumDriver::class,
            self::CORE                             => StableDiffusion\CoreDriver::class,
            self::ULTRA                            => StableDiffusion\UltraDriver::class,
            self::AWS_BEDROCK                      => StableDiffusion\AwsBedrockDriver::class,
            // OpenAI
            self::DAVINCI                     => OpenAI\DavinciDriver::class,
            self::TEXT_DAVINCI_003            => OpenAI\TextDavinciDriver::class,
            self::GPT_3_5_TURBO_16K           => OpenAI\GPT35Turbo16KDriver::class,
            self::GPT_3_5_TURBO               => OpenAI\GPT35TurboDriver::class,
            self::GPT_3_5_TURBO_0125          => OpenAI\GPT35Turbo0125Driver::class,
            self::GPT_3_5_TURBO_1106          => OpenAI\GPT35Turbo1106Driver::class,
            self::GPT_4_TURBO                 => OpenAI\GPT4TurboDriver::class,
            self::GPT_4                       => OpenAI\GPT4Driver::class,
            self::GPT_4_1106_PREVIEW          => OpenAI\GPT41106PreviewDriver::class,
            self::GPT_4_0125_PREVIEW          => OpenAI\GPT40125PreviewDriver::class,
            self::GPT_4_VISION_PREVIEW        => OpenAI\GPT4VisionPreviewDriver::class,
            self::TEXT_EMBEDDING_ADA_002      => OpenAI\TextEmbeddingAdaDriver::class,
            self::TEXT_EMBEDDING_3_SMALL      => OpenAI\TextEmbedding3SmallDriver::class,
            self::TEXT_EMBEDDING_3_LARGE      => OpenAI\TextEmbedding3LargeDriver::class,
            self::WHISPER_1                   => OpenAI\Whisper1Driver::class,
            self::DALL_E_2                    => OpenAI\DallE2Driver::class,
            self::DALL_E_3                    => OpenAI\DallE3Driver::class,
            self::TTS_1                       => OpenAI\TTS1Driver::class,
            self::TTS_1_HD                    => OpenAI\TTS1HDDriver::class,
            self::GPT_4_O                     => OpenAI\GPT4ODriver::class,
            self::GPT_4_O_MINI                => OpenAI\GPT4OMiniDriver::class,
            self::GPT_4_O_SEARCH_PREVIEW      => OpenAI\GPT4OSearchPreviewDriver::class,
            self::GPT_4_O_MINI_SEARCH_PREVIEW => OpenAI\GPT4OMiniSearchPreviewDriver::class,
            self::GPT_4_O1_PREVIEW            => OpenAI\GPT4O1PreviewDriver::class,
            self::GPT_4_O1_MINI               => OpenAI\GPT4O1MiniDriver::class,
            self::GPT_O_03_mini               => OpenAI\GPTO3MiniDriver::class,
            self::GPT_4_5_PREVIEW             => OpenAI\GPT45PreviewDriver::class,
            self::GPT_4_O_REALTIME_PREVIEW    => OpenAI\GPT4ORealtimePreviewDriver::class,
            // Anthropic
            self::CLAUDE_3_7_SONNET    => Anthropic\Claude37SonnetDriver::class,
            self::CLAUDE_3_5_HAIKU     => Anthropic\Claude35HaikuDriver::class,
            self::CLAUDE_3_5_SONNET_V2 => Anthropic\Claude35SonnetV2Driver::class,
            self::CLAUDE_3_5_SONNET    => Anthropic\Claude35SonnetDriver::class,
            self::CLAUDE_3_SONNET      => Anthropic\Claude3SonnetDriver::class,
            self::CLAUDE_3_OPUS        => Anthropic\Claude3OpusDriver::class,
            self::CLAUDE_3_HAIKU       => Anthropic\Claude3HaikuDriver::class,
            self::CLAUDE_2_1           => Anthropic\Claude21Driver::class,
            self::CLAUDE_2_0           => Anthropic\Claude20Driver::class,
            self::VOYAGE_2             => Anthropic\Voyage2Driver::class,
            self::VOYAGE_LARGE_2       => Anthropic\VoyageLarge2Driver::class,
            self::VOYAGE_CODE_2        => Anthropic\VoyageCode2Driver::class,
            // Gemini
            self::GEMINI_1_5_PRO_LATEST     => Gemini\Gemini15ProLatestDriver::class,
            self::GEMINI_PRO                => Gemini\GeminiProDriver::class,
            self::GEMINI_1_5_FLASH          => Gemini\Gemini15FlashDriver::class,
            self::GEMINI_PRO_VISION         => Gemini\GeminiProVisionDriver::class,
            self::GEMINI_TEXT_EMBEDING_004  => Gemini\GeminiTextEmbeding004Driver::class,
            // Deepseek
            self::DEEPSEEK_CHAT     => Deepseek\DeepseekChatDriver::class,
            self::DEEPSEEK_REASONER => Deepseek\DeepseekReasonerDriver::class,
            // Others
            self::CLIPDROP        => ClipDropDriver::class,
            self::NOVITA          => NovitaDriver::class,
            self::FREEPIK         => FreepikDriver::class,
            self::PLAGIARISMCHECK => PlagiarismCheckDriver::class,
            self::SYNTHESIA       => SynthesiaDriver::class,
            self::HEYGEN          => HeygenDriver::class,
            self::PEBBLELY        => PebblelyDriver::class,
            self::UNSPLASH        => UnsplashDriver::class,
            self::PEXELS          => PexelsDriver::class,
            self::PIXABAY         => PixabayDriver::class,
            self::ELEVENLABS      => ElevenLabs\ElevenlabsDriver::class,
            self::ISOLATOR        => ElevenLabs\IsolatorDriver::class,
            self::GOOGLE          => GoogleDriver::class,
            self::AZURE           => AzureDriver::class,
            self::Speechify       => SpeechifyDriver::class,
            self::SERPER          => SerperDriver::class,
            self::PERPLEXITY      => PerplexityDriver::class,
            // PiAPI
            self::MIDJOURNEY      => MidjourneyDriver::class,
            // X AI
            self::GROK_2_1212        => XAI\Grok21212Driver::class,
            self::GROK_2_VISION_1212 => XAI\Grok2Vision1212Driver::class,
            // FAL AI
            self::VEO_2                    => FalAI\Veo2Driver::class,
            self::FLUX_PRO                 => FalAI\FluxProDriver::class,
            self::IDEOGRAM                 => FalAI\IdeogramDriver::class,
            self::FLUX_PRO_1_1             => FalAI\FluxPro11Driver::class,
            self::FLUX_REALISM             => FalAI\FluxRealismDriver::class,
            self::FLUX_SCHNELL             => FalAI\FluxSchnellDriver::class,
            self::KLING                    => FalAI\KlingDriver::class,
            self::LUMA_DREAM_MACHINE       => FalAI\LumaDreamMachineDriver::class,
            self::HAIPER                   => FalAI\HaiperDriver::class,
            self::MINIMAX                  => FalAI\MinimaxDriver::class,
            self::VIDEO_UPSCALER	          => FalAI\VideoUpscalerDriver::class,
            self::COGVIDEOX_5B		           => FalAI\Cogvideox5bDriver::class,
            self::ANIMATEDIFF_V2V	         => FalAI\AnimatediffV2vDriver::class,
            self::FAST_ANIMATEDIFF_TURBO	  => FalAI\FastAnimatediffTurboDriver::class,
            // AI/ML api minimax engine
            self::MUSIC_01				 => AiMlMinimax\Music01Driver::class,
            // OpenRouter
            self::ANTHROPIC_CLAUDE_3_5_HAIKU_20241022                => OpenRouter\AnthropicClaude35Haiku20241022::class,
            self::ANTHROPIC_CLAUDE_3_5_HAIKU_20241022_SELF_MODERATED => OpenRouter\AnthropicClaude35Haiku20241022SelfModerated::class,
            self::ANTHROPIC_CLAUDE_3_5_HAIKU                         => OpenRouter\AnthropicClaude35Haiku::class,
            self::ANTHROPIC_CLAUDE_3_5_HAIKU_SELF_MODERATED          => OpenRouter\AnthropicClaude35HaikuSelfModerated::class,
            self::LUMIMAID_V02_70B                                   => OpenRouter\LumimaidV0270B::class,
            self::MAGNUM_V4_72B                                      => OpenRouter\MagnumV472B::class,
            self::XAI_GROK_BETA                                      => OpenRouter\XaiGrokBeta::class,
            self::MINISTRAL_8B                                       => OpenRouter\Ministral8B::class,
            self::MINISTRAL_3B                                       => OpenRouter\Ministral3B::class,
            self::QWEN25_7B_INSTRUCT                                 => OpenRouter\Qwen257BInstruct::class,
            self::NVIDIA_LLAMA_31_NEMOTRON_70B_INSTRUCT              => OpenRouter\NvidiaLlama31Nemotron70BInstruct::class,
            self::INFLECTION_3_PI                                    => OpenRouter\Inflection3Pi::class,
            self::INFLECTION_3_PRODUCTIVITY                          => OpenRouter\Inflection3Productivity::class,
            self::LIQUID_LFM_40B_MOE_FREE                            => OpenRouter\LiquidLfm40BMoeFree::class,
            self::LIQUID_LFM_40B_MOB                                 => OpenRouter\LiquidLfm40BMob::class,
            self::ROCINANTE_12B                                      => OpenRouter\Rocinante12B::class,
            self::EVA_QWEN25_14B                                     => OpenRouter\EvaQwen2514B::class,
            self::MAGNUM_V2_72B                                      => OpenRouter\MagnumV272B::class,
            self::META_LLAMA32_3B_INSTRUCT_FREE                      => OpenRouter\MetaLlama323BInstructFree::class,
            self::META_LLAMA32_1B_INSTRUCT_FREE                      => OpenRouter\MetaLlama321BInstructFree::class,
            self::META_LLAMA32_3B_INSTRUCT                           => OpenRouter\MetaLlama323BInstruct::class,
            self::META_LLAMA32_1B_INSTRUCT                           => OpenRouter\MetaLlama321BInstruct::class,
            self::PERPLEXITY_LLAMA_31_SONAR_405B_ONLINE              => OpenRouter\PerplexityLlama31Sonar405BOnline::class,
            self::PERPLEXITY_LLAMA_31_SONAR_70B_ONLINE               => OpenRouter\PerplexityLlama31Sonar70BOnline::class,
            self::PERPLEXITY_LLAMA_31_SONAR_70B                      => OpenRouter\PerplexityLlama31Sonar70B::class,
            self::PERPLEXITY_LLAMA_31_SONAR_8B_ONLINE                => OpenRouter\PerplexityLlama31Sonar8BOnline::class,
            self::PERPLEXITY_LLAMA_31_SONAR_8B                       => OpenRouter\PerplexityLlama31Sonar8B::class,
        };
    }

    /** @noinspection PhpDuplicateMatchArmBodyInspection */
    public function unitPrice(): float
    {
        return match ($this) {
            self::IMAGE_TO_VIDEO                 => 0.2,
            self::STABLE_DIFFUSION_XL_1024_V_1_0 => 0.009,
            self::STABLE_DIFFUSION_V_1_6         => 0.009,
            self::SD_3, self::ULTRA, self::SD_3_TURBO => 0.03,
            self::SD_3_MEDIUM        => 0.035,
            self::SD_3_LARGE         => 0.065,
            self::SD_3_LARGE_TURBO   => 0.04,
            self::SD_3_5_LARGE       => 0.065,
            self::SD_3_5_LARGE_TURBO => 0.04,
            self::SD_3_5_MEDIUM      => 0.035,
            self::CORE               => 0.02,
            self::AWS_BEDROCK        => 0.02,
            // OpenAI
            self::DAVINCI, self::GPT_3_5_TURBO_1106, self::GPT_3_5_TURBO => 0.00000266,
            self::TEXT_DAVINCI_003       => 0.0000266,
            self::GPT_3_5_TURBO_16K      => 0.00000532,
            self::GPT_3_5_TURBO_0125     => 0.000001995,
            self::GPT_4_TURBO, self::GPT_4_1106_PREVIEW, self::GPT_4_0125_PREVIEW, self::GPT_4_VISION_PREVIEW, self::TTS_1_HD => 0.0000399,
            self::GPT_4                  => 0.0000798,
            self::TEXT_EMBEDDING_ADA_002 => 0.0000000665,
            self::TEXT_EMBEDDING_3_SMALL => 0.0000000665,
            self::TEXT_EMBEDDING_3_LARGE => 0.0000000665,
            self::WHISPER_1              => 0.000000798,
            self::DALL_E_2               => 0.0000266,
            self::DALL_E_3               => 0.0001596,
            self::TTS_1, self::GPT_4_O   => 0.00001995,
            self::GPT_4_O_MINI                => 0.000000798,
            self::GPT_4_O_SEARCH_PREVIEW      => 0.0000798,
            self::GPT_4_O_MINI_SEARCH_PREVIEW => 0.0000798,
            self::GPT_4_O1_PREVIEW            => 0.0000798,
            self::GPT_4_O1_MINI               => 0.000005852,
            self::GPT_O_03_mini               => 0.000005852,
            self::GPT_4_5_PREVIEW             => 0.0001995,
            self::GPT_4_O_REALTIME_PREVIEW    => 0.0001064,
            // Anthropic
            self::CLAUDE_3_7_SONNET, self::CLAUDE_3_5_SONNET_V2, self::CLAUDE_3_5_SONNET, self::CLAUDE_3_SONNET => 0.00002,
            self::CLAUDE_3_5_HAIKU  => 0.00000533,
            self::CLAUDE_3_OPUS     => 0.00002,
            self::CLAUDE_3_HAIKU    => 0.00000533,
            self::CLAUDE_2_1, self::CLAUDE_2_0 => 0.024,
            self::VOYAGE_2, self::VOYAGE_LARGE_2, self::VOYAGE_CODE_2 => 0.00012,
            // Deepseek
            self::DEEPSEEK_CHAT     => 0.00000037,
            self::DEEPSEEK_REASONER => 0.00000292,
            // Others
            self::CLIPDROP         => 0.5,
            self::NOVITA           => 0.017,
            self::FREEPIK          => 0.20,
            self::PLAGIARISMCHECK  => 0.2,
            self::SYNTHESIA        => 2,
            self::HEYGEN           => 0.5,
            self::PEBBLELY         => 0.019,
            // Gemini
            self::GEMINI_1_5_PRO_LATEST, self::GEMINI_PRO_VISION => 0.00000667,
            self::GEMINI_PRO               => 0.00000667,
            self::GEMINI_1_5_FLASH         => 0.0000004,
            self::GEMINI_TEXT_EMBEDING_004 => 0.00001,
            // Unsplash
            self::UNSPLASH, self::PEXELS, self::PIXABAY => 0.01,
            // Elevenlabs
            self::ELEVENLABS, self::ISOLATOR => 0.3,
            // Google
            self::GOOGLE => 0.016,
            // Azure
            self::AZURE => 0.015,
            // Speechify
            self::Speechify => 0.015,
            // Serper
            self::SERPER => 0.001,
            // Perplexity
            self::PERPLEXITY => 0.001,
            // PiAPI
            self::MIDJOURNEY => 0.03,
            // X AI
            self::GROK_2_1212, self::GROK_2_VISION_1212 => 0.001,
            // FAL AI
            self::VEO_2, self::FLUX_PRO, self::FLUX_PRO_1_1, self::FLUX_REALISM, self::FLUX_SCHNELL, self::IDEOGRAM,
            self::KLING, self::LUMA_DREAM_MACHINE, self::HAIPER, self::MINIMAX => 0.05,
            self::VIDEO_UPSCALER, self::COGVIDEOX_5B, self::ANIMATEDIFF_V2V, self::FAST_ANIMATEDIFF_TURBO => 0.05,
            // AI/ML api minimax engine
            self::MUSIC_01	=> 0.05,
            // OpenRouter
            self::ANTHROPIC_CLAUDE_3_5_HAIKU_20241022                => 0.00000533,
            self::ANTHROPIC_CLAUDE_3_5_HAIKU_20241022_SELF_MODERATED => 0.00000533,
            self::ANTHROPIC_CLAUDE_3_5_HAIKU                         => 0.00000533,
            self::ANTHROPIC_CLAUDE_3_5_HAIKU_SELF_MODERATED          => 0.00000533,
            self::LUMIMAID_V02_70B                                   => 0.005,
            self::MAGNUM_V4_72B                                      => 0.005,
            self::XAI_GROK_BETA                                      => 0.005,
            self::MINISTRAL_8B                                       => 0.005,
            self::MINISTRAL_3B                                       => 0.005,
            self::QWEN25_7B_INSTRUCT                                 => 0.005,
            self::NVIDIA_LLAMA_31_NEMOTRON_70B_INSTRUCT              => 0.005,
            self::INFLECTION_3_PI                                    => 0.005,
            self::INFLECTION_3_PRODUCTIVITY                          => 0.005,
            self::LIQUID_LFM_40B_MOE_FREE                            => 0.005,
            self::LIQUID_LFM_40B_MOB                                 => 0.005,
            self::ROCINANTE_12B                                      => 0.005,
            self::EVA_QWEN25_14B                                     => 0.005,
            self::MAGNUM_V2_72B                                      => 0.005,
            self::META_LLAMA32_3B_INSTRUCT_FREE                      => 0.005,
            self::META_LLAMA32_1B_INSTRUCT_FREE                      => 0.005,
            self::META_LLAMA32_3B_INSTRUCT                           => 0.005,
            self::META_LLAMA32_1B_INSTRUCT                           => 0.005,
            self::PERPLEXITY_LLAMA_31_SONAR_405B_ONLINE              => 0.005,
            self::PERPLEXITY_LLAMA_31_SONAR_70B_ONLINE               => 0.005,
            self::PERPLEXITY_LLAMA_31_SONAR_70B                      => 0.005,
            self::PERPLEXITY_LLAMA_31_SONAR_8B_ONLINE                => 0.005,
            self::PERPLEXITY_LLAMA_31_SONAR_8B                       => 0.005,
        };
    }

    public static function getPlanLimits(EngineEnum $engine): Collection
    {
        return collect(self::listableCases())->filter(
            fn (EntityEnum $model) => $model->engine() === $engine
        )->mapWithKeys(
            fn (EntityEnum $model) => [
                $model->slug() => [
                    'credit'      => 0,
                    'isUnlimited' => false,
                ],
            ]
        );
    }

    public function subLabel(): string
    {
        $driverClass = $this->driverClass();

        return match (true) {
            class_interface_exists($driverClass, WithVideoToVideoInterface::class)  => __('Video'),
            class_interface_exists($driverClass, WithImagesInterface::class)        => __('Images'),
            class_interface_exists($driverClass, WithWordsInterface::class)         => __('Words'),
            class_interface_exists($driverClass, WithCharsInterface::class)         => __('Characters'),
            class_interface_exists($driverClass, WithImageToVideoInterface::class)  => __('Image to Video'),
            class_interface_exists($driverClass, WithTextToSpeechInterface::class)  => __('Text to Speech'),
            class_interface_exists($driverClass, WithSpeechToTextInterface::class)  => __('Speech to Text'),
            class_interface_exists($driverClass, WithTextToVideoInterface::class)   => __('Text to Video'),
            class_interface_exists($driverClass, WithVisionPreviewInterface::class) => __('Vision'),
            class_interface_exists($driverClass, WithPlagiarismInterface::class)    => __('Plagiarism'),
        };
    }

    public function tokenType(): AITokenType
    {
        $driverClass = $this->driverClass();

        return match (true) {
            class_interface_exists($driverClass, WithImagesInterface::class)         => AITokenType::IMAGE,
            class_interface_exists($driverClass, WithWordsInterface::class)          => AITokenType::WORD,
            class_interface_exists($driverClass, WithCharsInterface::class)          => AITokenType::CHARACTER,
            class_interface_exists($driverClass, WithImageToVideoInterface::class)   => AITokenType::IMAGE_TO_VIDEO,
            class_interface_exists($driverClass, WithTextToSpeechInterface::class)   => AITokenType::TEXT_TO_SPEECH,
            class_interface_exists($driverClass, WithSpeechToTextInterface::class)   => AITokenType::SPEECH_TO_TEXT,
            class_interface_exists($driverClass, WithTextToVideoInterface::class)    => AITokenType::TEXT_TO_VIDEO,
            class_interface_exists($driverClass, WithVideoToVideoInterface::class)   => AITokenType::VIDEO_TO_VIDEO,
            class_interface_exists($driverClass, WithVisionPreviewInterface::class)  => AITokenType::VISION,
            class_interface_exists($driverClass, WithPlagiarismInterface::class)     => AITokenType::PLAGIARISM
        };
    }

    public function tooltipHowToCalc(): string
    {
        $driverClass = $this->driverClass();

        return match (true) {
            class_interface_exists($driverClass, WithImagesInterface::class)        => __('1 Credit = 1 Image'),
            class_interface_exists($driverClass, WithWordsInterface::class),
            class_interface_exists($driverClass, WithSpeechToTextInterface::class),
            class_interface_exists($driverClass, WithVisionPreviewInterface::class),
            class_interface_exists($driverClass, WithPlagiarismInterface::class)    => __('1 Credit = 1 Word'),
            class_interface_exists($driverClass, WithCharsInterface::class)         => __('1 Credit = 1 Character'),
            class_interface_exists($driverClass, WithImageToVideoInterface::class),
            class_interface_exists($driverClass, WithVideoToVideoInterface::class),
            class_interface_exists($driverClass, WithTextToVideoInterface::class)   => __('1 Credit = 1 Video'),
            class_interface_exists($driverClass, WithTextToSpeechInterface::class)  => __('1 Credit = 1 Voice'),
        };
    }

    public static function entityDrivers(): array
    {
        return [
            WithImagesInterface::class,
            WithWordsInterface::class,
            WithCharsInterface::class,
            WithImageToVideoInterface::class,
            WithVideoToVideoInterface::class,
            WithTextToSpeechInterface::class,
            WithSpeechToTextInterface::class,
            WithTextToVideoInterface::class,
            WithVisionPreviewInterface::class,
            WithPlagiarismInterface::class,
        ];
    }

    public function defaultCreditForDemo(): float
    {
        $driverClass = $this->driverClass();

        return match (true) {
            class_interface_exists($driverClass, WithImagesInterface::class)     => 200,
            class_interface_exists($driverClass, WithWordsInterface::class),
            class_interface_exists($driverClass, WithCharsInterface::class)      => 5000,
            class_interface_exists($driverClass, WithVideoToVideoInterface::class),
            class_interface_exists($driverClass, WithImageToVideoInterface::class),
            class_interface_exists($driverClass, WithTextToSpeechInterface::class),
            class_interface_exists($driverClass, WithSpeechToTextInterface::class),
            class_interface_exists($driverClass, WithTextToVideoInterface::class),
            class_interface_exists($driverClass, WithVisionPreviewInterface::class),
            class_interface_exists($driverClass, WithPlagiarismInterface::class) => 100,
        };
    }

    public function isBetaEntity(): bool
    {
        return match ($this) {
            self::GPT_4_O1_PREVIEW,
            self::GPT_4_O1_MINI => true,
            default             => false,
        };
    }

    public static function embedingModels(EngineEnum $engineEnum): array
    {
        return match ($engineEnum) {
            EngineEnum::ANTHROPIC => [
                self::VOYAGE_2,
                self::VOYAGE_CODE_2,
                self::VOYAGE_LARGE_2,
            ],
            EngineEnum::GEMINI => [
                self::GEMINI_TEXT_EMBEDING_004,
            ],
            default => [
                self::TEXT_EMBEDDING_ADA_002,
                self::TEXT_EMBEDDING_3_SMALL,
                self::TEXT_EMBEDDING_3_LARGE,
            ]
        };
    }

    public function isEmbedding(): bool
    {
        return in_array($this, [
            self::TEXT_EMBEDDING_ADA_002,
            self::TEXT_EMBEDDING_3_SMALL,
            self::TEXT_EMBEDDING_3_LARGE,
            self::GEMINI_TEXT_EMBEDING_004,
            self::VOYAGE_2,
            self::VOYAGE_CODE_2,
            self::VOYAGE_LARGE_2,
        ]);
    }

    public static function reWriterModels(EngineEnum $engineEnum): array
    {
        return match ($engineEnum) {
            default => [
                self::DAVINCI,
                self::TEXT_DAVINCI_003,
                self::GPT_3_5_TURBO_16K,
                self::GPT_3_5_TURBO,
                self::GPT_3_5_TURBO_0125,
                self::GPT_4,
                self::GPT_4_1106_PREVIEW,
                self::GPT_4_0125_PREVIEW,
                self::GPT_4_TURBO,
                self::GPT_4_O,
                self::GPT_4_O_MINI,
                self::GPT_4_O_SEARCH_PREVIEW,
                self::GPT_4_O_MINI_SEARCH_PREVIEW,
                self::GPT_4_O1_PREVIEW,
                self::GPT_4_O1_MINI,
                self::GPT_O_03_mini,
                self::GPT_4_5_PREVIEW,
                self::DEEPSEEK_CHAT,
                self::DEEPSEEK_REASONER,
            ]
        };
    }

    public function keyAsString(): string
    {
        return (string) $this->name;
    }

    public function valueAsString(): string
    {
        return (string) $this->value;
    }
}
