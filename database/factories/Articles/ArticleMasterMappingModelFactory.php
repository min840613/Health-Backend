<?php

namespace Database\Factories\Articles;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class ArticleMasterMappingModelFactory
 * @package Database\Factories
 */
class ArticleMasterMappingModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'article_id' => fake()->randomNumber(),
            'master_id' => fake()->randomNumber(),
        ];
    }
}
