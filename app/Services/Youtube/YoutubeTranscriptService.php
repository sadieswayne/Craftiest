<?php

namespace App\Services\Youtube;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class YoutubeTranscriptService
{
    public string $searchapi = 'https://www.searchapi.io/api/v1/search';

    public function getTranscript($videoUrl, $defaultLang = 'en'): JsonResponse|array
    {
        if (setting('searchapi_api_for_youtube')) {
            return response()->json([
                'captions'    => $this->generateTranscript($videoUrl),
                'status_code' => 200,
            ]);
        }

        try {
            $response = Http::timeout(30)->get($videoUrl)
                ->onError(function ($response) {
                    return ['error' => 'IP blocked', 'status_code' => $response->getState()];
                })->body();

            $matches = [];
            preg_match('/"captionTracks":(\[.*?])/', $response, $matches);
            if (isset($matches[1])) {
                $captionTracks = json_decode($matches[1], true, 512, JSON_THROW_ON_ERROR);
                $captionTrack = collect($captionTracks)->firstWhere('languageCode', $defaultLang);

                if (! $captionTrack && isset($captionTracks[0]['baseUrl'])) {
                    $captionTrack = $captionTracks[0];
                }

                if ($captionTrack && isset($captionTrack['baseUrl'])) {
                    $baseUrl = html_entity_decode($captionTrack['baseUrl'], ENT_QUOTES, 'UTF-8');
                    $response = Http::get($baseUrl);
                    $captions = $response->body();

                    return response()->json([
                        'captions'    => $this->cleanYouTubeTranscript($captions)['text'],
                        'status_code' => 200,
                    ]);
                }

                return response()->json(['error' => __('No caption tracks found.'), 'status_code' => 404]);
            }

            return response()->json(['error' => __('No captionTracks found in the response.'), 'status_code' => 404]);
        } catch (Exception $e) {
            return ['error' => $e->getMessage(), 'status_code' => 500];
        }
    }

    public function cleanYouTubeTranscript(string $xmlString): array
    {
        $xml = @simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);

        if ($xml === false) {
            return [
                'array' => [],
                'text'  => '',
            ];
        }

        $transcriptArray = [];
        $cleanedTranscript = '';

        foreach ($xml->text as $textElement) {
            $start = (string) $textElement['start'];
            $dur = (string) $textElement['dur'];
            $text = trim((string) $textElement);

            $text = htmlspecialchars_decode($text, ENT_QUOTES);
            $text = preg_replace('/\s+/', ' ', $text);

            $transcriptArray[] = [
                'start'    => $start,
                'duration' => $dur,
                'text'     => $text,
            ];

            $cleanedTranscript .= $text . ' ';
        }

        return [
            'array' => $transcriptArray,
            'text'  => trim($cleanedTranscript),
        ];
    }

    public function generateTranscript(string $videoUrl): string
    {
        $videoId = $this->getYouTubeVideoId($videoUrl);

        if (! $videoId) {
            return response()->json(['error' => __('Video not found.'), 'status_code' => 404]);
        }

        if (! setting('searchapi_api_key')) {
            return response()->json(['error' => __('Searchapi API key is not set.'), 'status_code' => 404]);
        }

        $http = Http::get($this->searchapi, [
            'api_key'  => setting('searchapi_api_key'),
            'engine'   => 'youtube_transcripts',
            'video_id' => $videoId,
        ]);

        $transcripts = $http->json('transcripts');

        if ($transcripts) {
            $transcriptTexts = Arr::pluck($transcripts, 'text');

            if (empty($transcriptTexts)) {
                return response()->json(['error' => __('No transcript found.'), 'status_code' => 404]);
            }

            return implode(' ', $transcriptTexts);
        }

        return response()->json(['error' => __('Searchapi API key is not set.'), 'status_code' => 404]);
    }

    public function getYouTubeVideoId($url)
    {
        // URL'den sorgu parametrelerini ayrıştır
        $parts = parse_url($url);

        // Eğer 'v' parametresi varsa ID'yi döndür
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
            if (isset($query['v'])) {
                return $query['v'];
            }
        }

        // Eğer 'v' parametresi yoksa, URL yolunu kontrol et
        if (isset($parts['path'])) {
            $pathParts = explode('/', trim($parts['path'], '/'));

            return end($pathParts);
        }

        // Eğer ID bulunamazsa null döndür
        return null;
    }

    public function getSearchapi(): string
    {
        return $this->searchapi;
    }
}
