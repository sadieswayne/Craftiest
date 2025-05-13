<?php

namespace App\Extensions\OpenRouter\System\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class RouterAiService
{
    public const BASE_URL = 'https://openrouter.ai/api/v1/chat/completions';

    public function response($prompt, $model): JsonResponse|string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . setting('open_router_api'),
            'HTTP-Referer'  => config('app.url'),
            'X-Title'       => config('app.url'),
        ])
            ->post(self::BASE_URL, [
                'model'    => $model,
                'stream'   => true,
                'messages' => [
                    [
                        'role'    => 'user',
                        'content' => $prompt,
                    ],
                ],
            ]);

        if ($response->successful()) {
            return $response->body();
        }

        return response()->json(['error' => 'Request failed', 'details' => $response->body()], $response->status());
    }
}
