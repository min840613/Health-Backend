<?php

namespace Database\Factories\Masters;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class DivisionsModelFactory
 * @package Database\Factories\Masters
 */
class DivisionsModelFactory extends Factory
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
            'status' => fake()->randomElement([0, 1]),
            'icon' => fake()->imageUrl,
            'icon_hover' => fake()->imageUrl,
            'sort' => fake()->randomNumber(),
            'created_user' => fake()->name,
            'updated_user' => fake()->name,
        ];
    }

    /**
     * @return DivisionsModelFactory
     */
    public function published(): DivisionsModelFactory
    {
        return $this->state(function () {
            return [
                'status' => 1,
            ];
        });
    }
}
