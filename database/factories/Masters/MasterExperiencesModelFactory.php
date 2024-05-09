<?php

namespace Database\Factories\Masters;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class MasterExperiencesModelFactory
 * @package Database\Factories\Masters
 */
class MasterExperiencesModelFactory extends Factory
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
            'is_current_job' => fake()->randomElement([0, 1]),
            'created_user' => fake()->name,
            'updated_user' => fake()->name,
        ];
    }
}
