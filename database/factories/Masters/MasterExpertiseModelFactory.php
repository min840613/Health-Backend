<?php

namespace Database\Factories\Masters;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class MasterExpertiseModelFactory
 * @package Database\Factories\Masters
 */
class MasterExpertiseModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name,
            'created_user' => fake()->name,
            'updated_user' => fake()->name,
        ];
    }
}
