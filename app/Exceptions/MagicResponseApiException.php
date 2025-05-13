<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class MagicResponseApiException extends Exception
{
    protected array $data;

    public function __construct(array $data, int $code = 400, ?Exception $previous = null)
    {
        parent::__construct($data['message'], $code, $previous);

        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
