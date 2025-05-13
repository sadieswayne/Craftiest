<?php

namespace App\Http\Requests\Chatbot;

use App\Helpers\Classes\Helper;
use Illuminate\Foundation\Http\FormRequest;

class ChatbotRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            //            'role' => 'required|string',
            //            'first_message' => 'required|string',
            //            'instructions' => 'required|string',
            //            'logo' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            //            'width' => 'required|string',
            //            'height' => 'required|string',
            //            'color' => 'required|string',
            'model' => 'required|string',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'model' => Helper::setting('openai_default_model'),
        ]);
    }
}
