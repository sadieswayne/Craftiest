<?php

namespace App\Extensions\AIVideoToVideo\System\Services\Traits;

use App\Models\UserOpenai;

trait HasUserOpenai
{
    public UserOpenai $openai;

    public function setOpenai(UserOpenai $openai): self
    {
        $this->openai = $openai;

        return $this;
    }

    public function getOpenai(): UserOpenai
    {
        return $this->openai;
    }
}
