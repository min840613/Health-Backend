<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class DailyViewCountModelFactory
 * @package Database\Factories
 */
class DailyViewCountModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'date' => fake()->dateTimeBetween('- 2days', 'now'),
            'source_type' => fake()->word,
            'source_id' => fake()->randomNumber(),
            'click_count' => fake()->numberBetween(100, 3000),
        ];
    }
}
