<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Foundation\Vite as BaseVite;

class Vite extends BaseVite
{
    protected function assetPath($path, $secure = null)
    {
        $baseUrl = str_replace('http://', '//', $this->getDomain());

        $path = $baseUrl . '/' . ltrim($path, '/');

        return ($this->assetPathResolver ?? asset(...))($path, $secure);
    }

    private function getDomain()
    {
        if (file_exists(storage_path('installed'))) {
            $domain = config('app.url');
        } else {
            $domain = request()->getSchemeAndHttpHost();
        }

        return $domain;
    }
}
