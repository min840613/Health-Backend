<?php

namespace Database\Factories\Deepq;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class KeywordModelFactory
 */
class KeywordModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'keyword' => fake()->realText(20),
            'start_at' => now()->subDays(7)->toDateTimeString(),
            'end_at' => now()->toDateTimeString(),
            'count' => fake()->randomNumber(4),
            'created_user' => fake()->userName(),
            'updated_user' => fake()->userName(),
        ];
    }
}
