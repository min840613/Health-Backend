<?php

namespace Database\Factories\Masters;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class MastersModelFactory
 * @package Database\Factories\Masters
 */
class MastersModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status' => fake()->randomElement([0, 1]),
            'type' => fake()->randomElement([1, 2, 3]),
            'name' => fake()->name,
            'en_name' => fake()->userName,
            'image' => fake()->imageUrl,
            'description' => fake()->realText(100),
            'title' => fake()->text(50),
            'is_contracted' => fake()->boolean,
            'sort' => fake()->randomNumber(),
            'created_user' => fake()->name,
            'updated_user' => fake()->name,
        ];
    }

    /**
     * @return MastersModelFactory
     */
    public function published(): MastersModelFactory
    {
        return $this->state(function () {
            return [
                'status' => 1,
            ];
        });
    }
}
