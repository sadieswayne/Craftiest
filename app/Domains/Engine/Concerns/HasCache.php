<?php

declare(strict_types=1);

namespace App\Domains\Engine\Concerns;

trait HasCache
{
    public int $cacheTtl = 300;

    public function cache($key, $value)
    {
        return cache()->remember($key, $this->cacheTtl, $value);
    }
}
