<?php

namespace Database\Factories\ThirdPartyFeed;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class MixerboxArticleConditionModelFactory
 * @package Database\Factories
 */
class MixerboxArticleConditionModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'category_id' => fake()->randomNumber(3),
            'category_en_name' => fake()->name,
            'category_name' => fake()->name,
            'created_user' => fake()->name,
            'updated_user' => fake()->name,
        ];
    }
}
