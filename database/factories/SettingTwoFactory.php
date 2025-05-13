<?php

namespace Database\Factories;

use App\Models\SettingTwo;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingTwoFactory extends Factory
{
    protected $model = SettingTwo::class;

    public function definition(): array
    {
        return [
            'daily_voice_limit_enabled' => false,
        ];
    }
}
