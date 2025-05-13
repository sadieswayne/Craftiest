<?php

namespace App\Services\DeFi;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class DeFiNewsService
{
    public string $token = 'ba1fe293-f67a-454d-b4e4-a25242b64e5d';

    public function get()
    {
        $cacheKey = 'defi-news' . md5($this->token);

        return Cache::remember($cacheKey, 15, function () {

            $http = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
            ])->get('https://terminal.thetie.io/v1/trending_news');

            if ($http->failed()) {
                return [];
            }

            return array_filter($http->json('data'), function ($item) {
                return data_get($item, 'metadata.image') && data_get($item, 'metadata.description');
            });
        });
    }

    public function find(string $slug)
    {
        $data = $this->get();

        return collect($data)->firstWhere('link_hash', $slug);
    }
}
