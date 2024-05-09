<?php

namespace Database\Factories\ThirdPartyFeed;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class MixerboxArticlesModelFactory
 * @package Database\Factories
 */
class MixerboxArticlesModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'status' => fake()->randomElement([0, 1]),
            'release_date' => fake()->date,
            'created_user' => fake()->name,
            'updated_user' => fake()->name,
        ];
    }
}
