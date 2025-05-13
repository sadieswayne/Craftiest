<?php

namespace App\Http\Requests\Chatbot;

use Illuminate\Foundation\Http\FormRequest;

class ChatbotSettingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'chatbot_status'         => 'required',
            'chatbot_template'       => 'required',
            'position'               => 'required',
            'chatbot_position'       => 'required',
            'chatbot_rate_limit'     => 'required',
            'chatbot_login_required' => 'required',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'chatbot_position'       => $this->get('position'),
            'chatbot_login_required' => (bool) $this->get('chatbot_login_required'),
        ]);
    }
}
