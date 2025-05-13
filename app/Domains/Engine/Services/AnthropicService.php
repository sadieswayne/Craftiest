<?php

declare(strict_types=1);

namespace App\Domains\Engine\Services;

use App\Helpers\Classes\ApiHelper;
use App\Helpers\Classes\Helper;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class AnthropicService
{
    public ?string $key = null;

    public bool $stream = false;

    public array $messages = [];

    public ?string $system = null;

    public const ENDPOINT = 'https://api.anthropic.com/v1/messages';

    public function stream(): PromiseInterface|Response
    {
        $client = $this->client();

        $system = (bool) $this->system;

        $body = Helper::arrayMerge($system, [
            'model'      => setting('anthropic_default_model'),
            'max_tokens' => (int) setting('anthropic_max_output_length', 1024),
            'messages'   => $this->messages,
            'stream'     => $this->isStream(),
        ], [
            'system' => $this->system,
        ]);

        return $client->post(self::ENDPOINT, $body);
    }

    public function client(): PendingRequest
    {
        return Http::withHeaders([
            'x-api-key'         => $this->getKey(),
            'Accept'            => 'application/json',
            'Content-Type'      => 'application/json',
            'anthropic-version' => '2023-06-01',
        ]);
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getKey(): ?string
    {
        return $this->key ?? ApiHelper::setAnthropicKey();
    }

    public function isStream(): bool
    {
        return $this->stream;
    }

    public function setStream(bool $stream): self
    {
        $this->stream = $stream;

        return $this;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function setMessages(array $messages): self
    {
        $this->messages = $messages;

        return $this;
    }

    public function setSystem(?string $system): self
    {
        $this->system = $system;

        return $this;
    }
}
