<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class StableDiffusion
{
    protected Client $client;

    protected Response $response;

    protected string $accept = 'application/json';

    public function __construct(
        private string $apiKey,
        private ?string $organization = null,
        private ?string $stabilityClientId = null,
        private string $stabilityClientVersion = '1.2.1'
    ) {
        $this->client = new Client([
            'base_uri' => 'https://api.stability.ai/v1/',
            'headers'  => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept'        => $this->accept,
            ],
        ]);
    }

    public function generate(
        string $text,
        float $weight = 0.5,
        ?string $engine = null,
    ) {
        // engine: stable-diffusion-v1-6

        $response = $this->client->post('generation/stable-diffusion-v1-6/text-to-image', [
            'json' => [
                'text_prompts' => [[
                    'text'   => $text,
                    'weight' => $weight,
                ]],
            ],
        ]);

        $this->response = $response;

        return match ($this->accept) {
            'application/json' => json_decode($response->getBody()->getContents(), true),
            'image/png'        => $response->getBody()->getContents(),
        };
    }

    public function image()
    {
        $this->accept = 'image/png';

        return $this;
    }

    public function json()
    {
        return $this;
    }

    public function engines()
    {
        $response = $this->client->get('engines/list');

        return json_decode($response->getBody()->getContents(), true);
    }
}
