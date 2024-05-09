<?php

namespace Database\Factories\HomeArea;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class HomeTaxonModelFactory
 * @package Database\Factories
 */
class HomeTaxonModelFactory extends Factory
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
            'name' => fake()->name,
            'sort' => fake()->randomNumber(),
            'published_at' => fake()->dateTime,
            'published_end' => fake()->dateTime,
            'created_user' => fake()->randomNumber(),
            'updated_user' => fake()->randomNumber(),
        ];
    }

    public function published(): Factory
    {
        return $this->state(function () {
            return [
                'status' => 1,
            ];
        });
    }

    public function unpublished(): Factory
    {
        return $this->state(function () {
            return [
                'status' => 0,
            ];
        });
    }
}
