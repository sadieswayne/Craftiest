<?php

namespace App\Http\Requests\Admin\Settings;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\NoReturn;

class GeneralSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    #[NoReturn]
    protected function prepareForValidation(): void
    {
        $allData = $this->all();
        $entities = $this->entities;
        array_walk_recursive($entities, static function (&$value, $key) {
            switch ($key) {
                case 'credit':
                    $value = (int) $value; // Cast credit to integer

                    break;
                case 'isUnlimited':
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN); // Cast isUnlimited to boolean

                    break;
            }
        });

        $allData['entities'] = $entities;
        $this->replace($allData);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'entities.*.*.isUnlimited' => 'sometimes|boolean',
            'entities.*.*.credit'      => 'sometimes|numeric',
        ];
    }
}
