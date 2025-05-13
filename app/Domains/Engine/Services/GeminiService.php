<?php

namespace App\Domains\Engine\Services;

use App\Domains\Entity\Enums\EntityEnum;
use App\Helpers\Classes\ApiHelper;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class GeminiService
{
    public array $history = [];

    public const ENDPOINT = 'https://generativelanguage.googleapis.com/v1beta/models/';

    public function streamGenerateContent($entity = EntityEnum::GEMINI_1_5_FLASH->value): PromiseInterface|Response
    {
        ApiHelper::setGeminiKey();

        $client = $this->client();
        $body = [
            'contents' => $this->getHistory(),
        ];

        $url = sprintf('%s%s:streamGenerateContent?key=%s', self::ENDPOINT, $entity, config('gemini.api_key'));

        return $client->withOptions(['stream' => true])->post($url, $body);
    }

    public function generateContent($entity = EntityEnum::GEMINI_1_5_FLASH->value): PromiseInterface|Response
    {

        ApiHelper::setGeminiKey();

        $client = $this->client();
        $body = [
            'contents' => $this->getHistory(),
        ];

        $url = sprintf('%s%s:generateContent?key=%s', self::ENDPOINT, $entity, config('gemini.api_key'));

        return $client->post($url, $body);
    }

    public function client(): PendingRequest
    {
        return Http::withHeaders([
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Read a line from the stream.
     */
    public function readLine($stream): ?string
    {
        $buffer = '';
        $depth = 0;

        while (! $stream->eof()) {
            $char = $stream->read(1);

            if ($char === '{') {
                $depth++;
            } elseif ($char === '}') {
                $depth--;
            }

            $buffer .= $char;

            if ($depth === 0 && ! empty(trim($buffer))) {
                return $buffer;
            }
        }

        return null;
    }

    public function getHistory(): array
    {
        return $this->history;
    }

    public function setHistory(array $history): self
    {
        $this->history = $history;

        return $this;
    }
}
