<?php

namespace Database\Factories\Masters;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class MasterDivisionModelFactory
 * @package Database\Factories\Masters
 */
class MasterDivisionModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => fake()->text(200),
            'created_user' => fake()->name,
            'updated_user' => fake()->name,
        ];
    }
}
