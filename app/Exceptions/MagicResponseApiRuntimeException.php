<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class MagicResponseApiRuntimeException extends RuntimeException
{
    protected array $data;

    public function __construct(array $data, int $code = 400, ?RuntimeException $previous = null)
    {
        parent::__construct($data['message'], $code, $previous);

        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
