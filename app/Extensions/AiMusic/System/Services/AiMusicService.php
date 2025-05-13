<?php

namespace App\Extensions\AiMusic\System\Services;

use App\Domains\Engine\Enums\EngineEnum;
use App\Helpers\Classes\Helper;
use App\Models\Setting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class AiMusicService
{
    public const BASE_URL = 'https://api.aimlapi.com/v2/generate/audio/' . EngineEnum::AI_ML_MINIMAX->value;

    public const CLIP_GPT_URL = '/generate';

    public const UPLOAD_VOICE_URL = '/upload';

    protected mixed $apiKey;

    protected mixed $model;

    public function __construct()
    {
        ini_set('max_execution_time', 300);
        set_time_limit(300);
        $this->apiKey = Setting::getCache()->aimlapi_key;
        $this->model = Setting::getCache()->ai_music_model;
    }

    public function generateSong(Request $request): array
    {
        try {
            if ($request->hasFile('audio')) {
                $filePath = $request->file('audio')->store('uploads', 'local');
                $fileContent = Storage::get($filePath);
                $fileName = $request->file('audio')->getClientOriginalName();
                Storage::delete($filePath);
            } else {
                $audioUrl = $request->input('link');
                $audioResponse = Http::get($audioUrl);
                if (! $audioResponse->successful()) {
                    return [
                        'gen_status'  => 'error',
                        'message'     => __('Failed to fetch file from the provided link.'),
                    ];
                }
                $fileContent = $audioResponse->body();
                $fileName = time() . '.mp3';
            }

            $response = Http::withHeaders([
                'Authorization' => "Bearer $this->apiKey",
            ])->attach('file', $fileContent, $fileName)
                ->post(self::BASE_URL . self::UPLOAD_VOICE_URL, [
                    'purpose' => $request->input('purpose'),
                ]);

            $responseData = $response->json();
            if (! $response->successful()) {
                return [
                    'gen_status'  => 'error',
                    'message'     => __('Upload failed: ') . $response->body(),
                ];
            }

            if (isset($responseData['base_resp']['status_code']) && $responseData['base_resp']['status_code'] !== 0) {
                return [
                    'gen_status'  => 'error',
                    'message'     => $responseData['base_resp']['status_msg'],
                ];
            }

            return $this->generateLyrics($request->input('lyrics'), $responseData);
        } catch (Exception $e) {
            return [
                'gen_status'  => 'error',
                'message'     => __('An unexpected error occurred: ') . $e->getMessage(),
            ];
        }
    }

    private function generateLyrics(?string $lyrics, array $response): array
    {
        if (empty($response['voice_id']) || empty($response['instrumental_id'])) {
            return [
                'gen_status'  => 'error',
                'message'     => __('Missing required audio components.'),
            ];
        }
        $payload = [
            'refer_voice'        => $response['voice_id'],
            'refer_instrumental' => $response['instrumental_id'],
            'lyrics'             => $lyrics ?? '',
            'model'              => $this->model,
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->post(Helper::parseUrl(self::BASE_URL, self::CLIP_GPT_URL), $payload);

            $data = $response->json();
            if (empty($data['data']['audio']) || ! $response->successful()) {
                return [
                    'gen_status'  => 'error',
                    'message'     => __('Audio generation failed. Please try again.'),
                ];
            }

            $fileName = time() . auth()->user()->id . '_audio.mp3';
            Storage::disk('public')->put($fileName, hex2bin($data['data']['audio']));
            $path = '/uploads/' . $fileName;

            return [
                'gen_status'  => 'success',
                'message'     => __('Audio generated successfully'),
                'status'      => 'complete',
                'audio_url'   => $path,
                'trace_id'    => $response['trace_id'],
            ];

        } catch (Exception $e) {
            return [
                'status'  => 'error',
                'message' => __('An unexpected error occurred: ') . $e->getMessage(),
            ];
        }
    }
}
