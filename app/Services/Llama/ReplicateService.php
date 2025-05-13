<?php

namespace App\Services\Llama;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Http;

class ReplicateService
{
    public const ENDPOINT = 'https://api.replicate.com/v1/models/meta/meta-llama-3.1-405b-instruct/predictions';

    public function makePrediction($message)
    {
        return Http::acceptJson()
            ->withToken(setting('llama_api_key'))
            ->timeout(60)
            ->post(self::ENDPOINT, [
                'stream' => true,
                'input'  => [
                    'prompt'     => $message,
                    'max_tokens' => (int) setting('llama_max_output_length', 500),
                ],
            ])->json();
    }

    /**
     * @throws GuzzleException
     */
    public function stream($streamUrl): array
    {
        $client = new Client;

        $headers = [
            'Accept'        => 'text/event-stream',
            'Cache-Control' => 'no-store',
            'Authorization' => 'Bearer ' . setting('llama_api_key'),
        ];

        $response = $client->request('GET', $streamUrl, [
            'headers' => $headers,
            'stream'  => true,
            'timeout' => 60,
        ]);

        $body = $response->getBody();
        $responsedText = '';
        $total_used_tokens = 0;

        while (! $body->eof()) {
            $data = $body->read(1024);

            $output = str_replace('{}', '', $data);

            $lines = explode("\n", $output);

            foreach ($lines as $line) {
                if (str_starts_with($line, 'data: ')) {
                    $part = (substr($line, 6));
                    $responsedText .= $part;
                    $total_used_tokens += countWords($part);
                }
            }

            echo $output;
            ob_flush();
            flush();

            if (str_contains($data, 'event: done')) {
                break;
            }
        }

        return ['responsedText' => $responsedText, 'tokens' => $total_used_tokens];
    }
}
