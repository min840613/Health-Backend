<?php

namespace Database\Factories\Articles;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class ArticleCategoriesMappingsModelFactory
 * @package Database\Factories
 */
class ArticleCategoriesMappingsModelFactory extends Factory
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
            'category_id' => fake()->randomNumber(),
            'sort' => fake()->randomNumber(),
            'parent' => 0,
        ];
    }
}
