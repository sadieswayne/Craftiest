<?php

namespace App\Jobs\Voice;

use App\Models\SettingTwo;
use App\Models\Voice\ElevenlabVoice;
use CURLFile;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ElevenlabVoiceCreateJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public int $eloquentId
    ) {}

    public function handle(): void
    {
        $elevenlabVoice = ElevenlabVoice::query()->find($this->eloquentId);

        $setting = SettingTwo::query()->first();

        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://api.elevenlabs.io/v1/voices/add');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: multipart/form-data',
                'Xi-api-key: ' . $setting?->getAttribute('elevenlabs_api_key'),
            ]);

            $postFields = [
                'files' => new CURLFile(storage_path('app/' . $elevenlabVoice->getAttribute('path'))),
                'name'  => $elevenlabVoice->getAttribute('name'),
            ];

            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

            $result = curl_exec($ch);

            curl_close($ch);

            $data = json_decode($result, true);

            if ($voiceId = data_get($data, 'voice_id')) {
                $elevenlabVoice->setAttribute('voice_id', $voiceId);
                $elevenlabVoice->save();
            }
        } catch (Exception $e) {
            return;
        }

    }
}
