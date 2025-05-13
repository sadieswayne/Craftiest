<?php

namespace App\Extensions\Midjourney\System\Services;

use App\Domains\Entity\Enums\EntityEnum;
use App\Helpers\Classes\ApiHelper;
use App\Helpers\Classes\Helper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class PiAPIService
{
    public static function generate(string $prompt): string
    {
        $http = Http::withHeaders([
            'x-api-key'    => ApiHelper::setPiAPIKey(),
            'Content-Type' => 'application/json',
        ])->post('https://api.piapi.ai/api/v1/task', [
            'model'     => EntityEnum::MIDJOURNEY->value,
            'task_type' => 'imagine',
            'input'     => [
                'prompt'           => $prompt,
                'aspect_ratio'     => '1:1',
                'process_mode'     => 'turbo',
                'skip_prompt_check'=> false,
            ],
            'config' => [
                // route('generator.webhook.midjourney')
                'webhook_config' => [
                    'endpoint' => 'https://magicai.requestcatcher.com/webhook',
                    'secret'   => 'secret',
                ],
            ],
        ]);

        if ($http->ok() && $http->json('code') === 200) {
            return $http->json('data.task_id');
        }

        if ($http->json('code') === 500 && $http->json('data.error.raw_message')) {
            throw new RuntimeException($http->json('data.error.raw_message'));
        }

        throw new RuntimeException(__('Check your PiAPI key.'));
    }

    public static function check($taskId)
    {
        $http = Http::withHeaders([
            'x-api-key'    => ApiHelper::setPiAPIKey(),
            'Content-Type' => 'application/json',
        ])->get('https://api.piapi.ai/api/v1/task/' . $taskId);

        if ($http->ok() && $http->json('code') === 200) {
            return $http->json('data.output.image_url');
        }

        return null;
    }

    public static function downloadImageToStorage($url = null, $filename = null)
    {
        if (! $url) {
            return null;
        }

        // Resmi URL'den indir
        $response = Http::get($url);

        // Eğer dosya başarıyla indirildiyse devam et
        if ($response->successful()) {
            // Dosya içeriğini al
            $fileContent = $response->body();

            // Dosya uzantısını belirleyin
            $extension = pathinfo($url, PATHINFO_EXTENSION);

            // Eğer dosya adı verilmemişse, bir dosya adı oluşturun
            if (! $filename) {
                $filename = uniqid('image_') . '.' . $extension;
            } else {
                $filename .= '.' . $extension;
            }

            $image_storage = Helper::settingTwo('ai_image_storage');

            if ($image_storage === 'r2') {
                Storage::disk('r2')->put($filename, $fileContent);

                return Storage::disk('r2')->url($filename);
            } elseif ($image_storage === 's3') {

                Storage::disk('s3')->put($filename, $fileContent);

                return Storage::disk('s3')->url($filename);
            }

            // save file on local storage or aws s3
            Storage::disk('thumbs')->put($filename, $fileContent);

            $dump = Storage::disk('public')->put($filename, $fileContent);

            if ($dump) {
                return '/uploads/' . $filename;
            }

            return 'error';
        }

        // return false when fail
        return null;
    }
}
