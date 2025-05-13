<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition(): array
    {
        return [
            'active'               => $this->faker->boolean(),
            'name'                 => $this->faker->name(),
            'price'                => $this->faker->randomFloat(),
            'currency'             => $this->faker->word(),
            'frequency'            => $this->faker->word(),
            'is_featured'          => $this->faker->boolean(),
            'stripe_product_id'    => $this->faker->word(),
            'ai_name'              => $this->faker->name(),
            'max_tokens'           => $this->faker->randomNumber(),
            'can_create_ai_images' => $this->faker->boolean(),
            'plan_type'            => $this->faker->word(),
            'features'             => $this->faker->word(),
            'type'                 => $this->faker->word(),
            'created_at'           => Carbon::now(),
            'updated_at'           => Carbon::now(),
            'trial_days'           => $this->faker->randomNumber(),
            'is_team_plan'         => $this->faker->boolean(),
            'plan_allow_seat'      => $this->faker->randomNumber(),
            'open_ai_items'        => $this->faker->words(),
            'description'          => $this->faker->text(),
            'plan_ai_tools'        => [],
            'plan_features'        => [],
            'ai_models'            => [],
        ];
    }
}
