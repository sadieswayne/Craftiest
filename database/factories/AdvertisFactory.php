<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Advertis>
 */
class AdvertisFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key'           => str($this->faker->unique()->word())->snake(),
            'title'         => $this->faker->sentence(),
            'tracking_code' => [
                'mobile'  => $this->faker->randomHtml(),
                'desktop' => $this->faker->randomHtml(),
                'tablet'  => $this->faker->randomHtml(),
            ],
            'status' => $this->faker->boolean(),
        ];
    }
}
