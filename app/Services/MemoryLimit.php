<?php

namespace App\Services;

use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class MemoryLimit extends Check
{
    public function run(): Result
    {
        $memoryLimit = getServerMemoryLimit();

        $result = \Spatie\Health\Checks\Result::make();

        if ($memoryLimit == -1) {
            return $result->ok(__('Unlimited'));
        }

        if ($memoryLimit < 64) {
            return $result->failed("{$memoryLimit}M");
        }

        return $result->ok("{$memoryLimit}M");
    }
}
