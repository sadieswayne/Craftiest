<?php

namespace App\Http\Requests\User\Brand;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BrandRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_id'         => 'required|integer',
            'name'            => 'required|string|max:255',
            'website'         => 'sometimes|nullable',
            'tagline'         => 'sometimes|nullable|string',
            'description'     => 'required|string',
            'brand_color'     => 'sometimes|nullable|string',
            'industry'        => 'sometimes|nullable|string',
            'tone_of_voice'   => 'sometimes|nullable|string',
            'target_audience' => 'sometimes|nullable|string',

            'inputNames'    => 'sometimes',
            'inputFeatures' => 'sometimes',
            'inputTypes'    => 'sometimes',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id'	        => Auth::id(),
            'name'            => $this->c_name,
            'website'         => $this->c_website,
            'tagline'         => $this->c_tagline,
            'description'     => $this->c_description,
            'brand_color'     => $this->c_color,
            'industry'        => $this->c_industry,
            'tone_of_voice'   => $this->tone_of_voice,
            'target_audience' => $this->target_audience,
            'inputNames'      => explode(',', $this->input_name),
            'inputFeatures'   => explode(',', $this->input_features),
            'inputTypes'      => explode(',', $this->input_type),
        ]);
    }
}
