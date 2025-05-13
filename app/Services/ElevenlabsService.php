<?php

namespace App\Services;

use App\Models\SettingTwo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class ElevenlabsService
{
    public const URL = 'https://api.elevenlabs.io/v1/voices';

    protected ?string $apiKey;

    public function __construct()
    {
        $this->apiKey = SettingTwo::query()->first()?->elevenlabs_api_key;
    }

    public function getVoices(): array|Collection
    {
        $response = Http::withHeaders([
            'xi-api-key' => $this->apiKey,
        ])->timeout(30)
            ->get(self::URL);

        if ($response->failed()) {
            return [];
        }

        $data = $response->json();

        return collect($data['voices'])->map(function ($voice) {
            return [
                'voice_id'    => $voice['voice_id'],
                'name'        => $voice['name'],
                'preview_url' => $voice['preview_url'],
            ];
        });
    }
}
