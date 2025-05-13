<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Traits\EnumTo;
use App\Enums\Traits\IntBackedEnumTrait;
use App\Exceptions\MagicResponseApiException;
use App\Exceptions\MagicResponseApiRuntimeException;
use Exception;
use Illuminate\Http\JsonResponse;
use RuntimeException;

enum MagicResponse: int implements Contracts\WithIntBackedEnum
{
    use EnumTo;
    use IntBackedEnumTrait;

    case NO_CREDITS_LEFT = 419;

    public function statusCode(): int
    {
        return $this->value;
    }

    public function status(): string
    {
        return match ($this) {
            self::NO_CREDITS_LEFT  => 'error',
        };
    }

    public function responseType(): string
    {
        return match ($this) {
            self::NO_CREDITS_LEFT  => 'json',
        };
    }

    public function message(): string|array
    {
        return match ($this) {
            self::NO_CREDITS_LEFT  => [
                'message' => __('You have no credits left. Please consider upgrading your plan.'),
                'status'  => $this->status(),
            ],
        };
    }

    public function response(): JsonResponse
    {
        return match ($this->responseType()) {
            'json'  => response()->json($this->message(), $this->statusCode()),
            default => response($this->getMessageText(), $this->statusCode()),
        };
    }

    public function exceptionsAsJson(): bool
    {
        return false;
    }

    /**
     * @noinspection ThrowRawExceptionInspection
     *
     * @throws Exception
     */
    public function exception(): void
    {
        $message = $this->message();

        if ($this->exceptionsAsJson()) {
            throw new MagicResponseApiException($message, $this->statusCode());
        }

        throw new Exception($message['message'], $this->statusCode());
    }

    /**
     * @throws RuntimeException
     */
    public function runtimeException(): void
    {
        $message = $this->message();

        if ($this->exceptionsAsJson()) {
            throw new MagicResponseApiRuntimeException($message, $this->statusCode());
        }

        throw new RuntimeException($message['message'], $this->statusCode());
    }

    private function getMessageText(): string
    {
        $message = $this->message();

        return match ($this->responseType()) {
            'json'  => $message['message'],
            default => $message,
        };
    }

    public function abort(): void
    {
        match ($this->responseType()) {
            'json'  => abort($this->response()->setStatusCode($this->statusCode()), $this->getMessageText()),
            default => abort($this->statusCode(), $this->getMessageText()),
        };
    }

    public function abort_if(bool $condition): void
    {
        if ($condition) {
            $this->abort();
        }
    }

    public function abort_unless(bool $condition): void
    {
        if (! $condition) {
            $this->abort();
        }
    }
}
