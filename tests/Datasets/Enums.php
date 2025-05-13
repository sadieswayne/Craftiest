<?php

declare(strict_types=1);

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Entity\Enums\EntityEnum;

dataset('bool', [
    true,
    false,
]);

dataset('entities', EntityEnum::cases());

dataset('ai_engines', EngineEnum::cases());
