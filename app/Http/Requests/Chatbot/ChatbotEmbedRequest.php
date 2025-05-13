<?php

declare(strict_types=1);

namespace App\Http\Requests\Chatbot;

use Illuminate\Foundation\Http\FormRequest;

class ChatbotEmbedRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'appKey' => 'required|uuid',
        ];
    }
}
