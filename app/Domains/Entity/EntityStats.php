<?php

declare(strict_types=1);

namespace App\Domains\Entity;

use App\Domains\Entity\Contracts\Calculate\WithCharsInterface;
use App\Domains\Entity\Contracts\Calculate\WithImagesInterface;
use App\Domains\Entity\Contracts\Calculate\WithImageToVideoInterface;
use App\Domains\Entity\Contracts\Calculate\WithPlagiarismInterface;
use App\Domains\Entity\Contracts\Calculate\WithSpeechToTextInterface;
use App\Domains\Entity\Contracts\Calculate\WithTextToSpeechInterface;
use App\Domains\Entity\Contracts\Calculate\WithTextToVideoInterface;
use App\Domains\Entity\Contracts\Calculate\WithVisionPreviewInterface;
use App\Domains\Entity\Contracts\Calculate\WithWordsInterface;
use App\Domains\Entity\Enums\EntityEnum;
use Illuminate\Support\Collection;

class EntityStats
{
    private static function makeStat(string $type): EntityStatItem
    {
        return new EntityStatItem($type);
    }

    public static function all(): Collection
    {
        return collect(EntityEnum::entityDrivers())->map(function ($driver) {
            return self::makeStat($driver);
        });
    }

    public static function speechToText(): EntityStatItem
    {
        return self::makeStat(WithSpeechToTextInterface::class);
    }

    public static function imageToVideo(): EntityStatItem
    {
        return self::makeStat(WithImageToVideoInterface::class);
    }

    public static function image(): EntityStatItem
    {
        return self::makeStat(WithImagesInterface::class);
    }

    public static function char(): EntityStatItem
    {
        return self::makeStat(WithCharsInterface::class);
    }

    public static function word(): EntityStatItem
    {
        return self::makeStat(WithWordsInterface::class);
    }

    public static function textToSpeech(): EntityStatItem
    {
        return self::makeStat(WithTextToSpeechInterface::class);
    }

    public static function visionPreview(): EntityStatItem
    {
        return self::makeStat(WithVisionPreviewInterface::class);
    }

    public static function textToVideo(): EntityStatItem
    {
        return self::makeStat(WithTextToVideoInterface::class);
    }

    public static function plagiarism(): EntityStatItem
    {
        return self::makeStat(WithPlagiarismInterface::class);
    }

    public static function type(string $type): EntityStatItem
    {
        return self::makeStat($type);
    }
}
