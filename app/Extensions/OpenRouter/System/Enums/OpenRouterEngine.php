<?php

namespace App\Extensions\OpenRouter\System\Enums;

use App\Enums\Contracts\WithStringBackedEnum;
use App\Enums\Traits\EnumTo;
use App\Enums\Traits\StringBackedEnumTrait;

enum OpenRouterEngine: string implements WithStringBackedEnum
{
    use EnumTo;
    use StringBackedEnumTrait;

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

    public function label(): string
    {
        return match ($this) {
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
}
