<?php

namespace Database\Factories\Categories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class MenuListModelFactory
 * @package Database\Factories
 */
class SubCategoriesModelFactory extends Factory
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
            'en_name' => fake()->name,
            'created_user' => 'test',
            'updated_user' => 'test',
        ];
    }
}
