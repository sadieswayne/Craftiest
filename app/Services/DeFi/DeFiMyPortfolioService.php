<?php

namespace App\Services\DeFi;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class DeFiMyPortfolioService
{
    public function data(string $token)
    {
        $tokens = explode(',', $token);

        $key = (count($tokens) > 1) ? 'wallets' : 'wallet';

        $cacheKey = 'defi-my-portfolio' . md5($token . $key);

        $data = $this->dataCache($cacheKey, $token, $key);

        if (! $data) {
            Cache::forget($cacheKey);
        }

        return $this->dataCache($cacheKey, $token, $key);
    }

    public function dataCache($cacheKey, $token, $key)
    {
        return Cache::remember($cacheKey, 10000, function () use ($token, $key) {
            $http = Http::get('https://api.mobula.io/api/1/wallet/portfolio', [
                $key => $token,
            ]);

            if ($http->failed()) {
                return [];
            }

            return $http->json('data');
        });
    }
}
