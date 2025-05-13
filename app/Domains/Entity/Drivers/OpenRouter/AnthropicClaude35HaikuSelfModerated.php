<?php

declare(strict_types=1);

namespace App\Domains\Entity\Drivers\OpenRouter;

use App\Domains\Entity\BaseDriver;
use App\Domains\Entity\Concerns\Calculate\HasWords;
use App\Domains\Entity\Concerns\Input\HasInput;
use App\Domains\Entity\Contracts\Calculate\WithWordsInterface;
use App\Domains\Entity\Contracts\Input\WithInputInterface;
use App\Domains\Entity\Enums\EntityEnum;

class AnthropicClaude35HaikuSelfModerated extends BaseDriver implements WithInputInterface, WithWordsInterface
{
    use HasInput;
    use HasWords;

    public function enum(): EntityEnum
    {
        return EntityEnum::ANTHROPIC_CLAUDE_3_5_HAIKU_SELF_MODERATED;
    }
}
