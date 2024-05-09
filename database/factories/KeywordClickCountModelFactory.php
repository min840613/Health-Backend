<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KeywordClickCountModel>
 */
class KeywordClickCountModelFactory extends Factory
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
            'keyword' => fake()->randomElement(['A1', 'A2', 'A3', 'A4', 'A5', 'A6']),
            'click_count' => fake()->numberBetween(1000, 3000),
        ];
    }


}
