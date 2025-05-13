<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Traits\EnumTo;
use App\Enums\Traits\StringBackedEnumTrait;

enum AITokenType: string implements Contracts\WithStringBackedEnum
{
    use EnumTo;
    use StringBackedEnumTrait;

    case WORD = 'word';
    case IMAGE = 'image';

    case CHARACTER = 'character';
    case IMAGE_TO_VIDEO = 'image_to_video';
    case TEXT_TO_SPEECH = 'text_to_speech';
    case SPEECH_TO_TEXT = 'speech_to_text';
    case TEXT_TO_VIDEO = 'text_to_video';

    case VIDEO_TO_VIDEO = 'video_to_video';
    case VISION = 'vision';
    case PLAGIARISM = 'plagiarism';

    public function label(): string
    {
        return match ($this) {
            self::WORD  => __('Word'),
            self::IMAGE => __('Image'),
        };
    }
}
