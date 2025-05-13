<?php

namespace App\Extensions\AIVideoToVideo\System\Services;

use App\Helpers\Classes\ApiHelper;
use App\Helpers\Classes\Helper;
use App\Models\UserOpenai;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

abstract class BaseService
{
    use Traits\HasUserOpenai;

    public const STORAGE_S3 = 's3';

    public const CLOUDFLARE_R2 = 'r2';

    public const GENERATE_ENDPOINT = 'https://queue.fal.run/fal-ai/%s';

    public function request(UserOpenai $openai, array $data = [], ?string $model = null): bool
    {
        $model = $model ?: $openai['payload']['model'];

        $http = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
            'Authorization' => 'Key ' . ApiHelper::setFalAIKey(),
        ])->post(sprintf(self::GENERATE_ENDPOINT, $model), $data);

        if ($http->ok()) {
            if ($http->json('status') && $requestId = $http->json('request_id')) {
                return $openai->update([
                    'request_id' => $requestId,
                    'status'     => 'IN_QUEUE',
                ]);
            }
        }

        $openai->update(['status' => 'failed']);

        $detail = $http->json('detail');

        throw ValidationException::withMessages([
            'output' => trans($detail ?: 'Failed to generate video'),
        ]);
    }

    abstract public function generate();

    public function checked(): array|\Illuminate\Http\JsonResponse
    {
        $openai = $this->getOpenai();

        $model = $openai['payload']['model'];

        $http = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
            'Authorization' => 'Key ' . ApiHelper::setFalAIKey(),
        ])->get("https://queue.fal.run/fal-ai/$model/requests/" . $openai['request_id']);

        if ($http->ok()) {
            if ($http->json('video')) {
                $video = $http->json('video.url');

                $client = new Client([
                    'base_uri' => $video,
                ]);

                $response = $client->request('GET');

                if ($response->getStatusCode() === 200) {
                    $fileContents = $response->getBody()->getContents();

                    $nameOfImage = 'image-to-video-' . Str::random(12) . '.mp4';
                    Storage::disk('public')->put($nameOfImage, $fileContents);
                    $path = 'uploads/' . $nameOfImage;
                    $imageStorage = Helper::settingTwo('ai_image_storage');
                    if ($imageStorage === self::STORAGE_S3) {
                        try {
                            $uploadedFile = new File($path);
                            $aws_path = Storage::disk(self::STORAGE_S3)->put('', $uploadedFile);
                            unlink($path);
                            $path = Storage::disk(self::STORAGE_S3)->url($aws_path);
                        } catch (Exception $e) {
                            return ['status' => 'error', 'message' => 'AWS Error - ' . $e->getMessage()];
                        }
                    }

                    $openai->update([
                        'output'    => $imageStorage === self::STORAGE_S3 ? $path : '/' . $path,
                        'storage'   => $imageStorage === self::STORAGE_S3 ? UserOpenai::STORAGE_AWS : UserOpenai::STORAGE_LOCAL,
                        'status'    => 'COMPLETED',
                    ]);

                    return ['status' => 'finished', 'url' => $path, 'video' => $openai];
                }
            }

        }

        if ($http->json('request_id')) {
            return ['type' => 'success', 'status' => 'in-progress'];
        }

        $openai->delete();

        return response()->json(['status' => 'error', 'ok' => $http->ok(),  'message' => 'Failed to generate video', 'json' => $http->json()]);
    }
}
