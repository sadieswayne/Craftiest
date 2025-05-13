<?php

namespace App\Http\Requests\Chatbot;

use Illuminate\Foundation\Http\FormRequest;

class ChatbotTrainingRequest extends FormRequest
{
    public function rules(): array
    {
        $array = [
            'type' => 'required|string',
        ];

        if ($this->input('type') == 'url' || $this->input('type') == 'pdf') {
            $array['chatbot_data'] = 'required|array';
            $array['chatbot_data.*'] = 'sometimes|nullable|integer';
        }

        return $array;
    }

    protected function prepareForValidation(): void {}
}
