<?php

namespace Database\Factories\Categories;

use App\Enums\MainCategoriesType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class MainCategoriesModelFactory
 * @package Database\Factories
 */
class MainCategoriesModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'categories_id' => fake()->randomNumber(3),
            'categories_type' => fake()->randomElement([1, 2, 3]),
            'publish' => fake()->dateTime,
            'name' => fake()->name,
            'en_name' => fake()->name,
            'meta_title' => fake()->name,
            'description' => fake()->name,
            'image' => fake()->imageUrl,
            'categories_status' => fake()->randomElement([0, 1]),
            'show_category_menu' => fake()->randomElement([0, 1]),
            'sort_index' => fake()->randomNumber(),
            'target' => fake()->randomElement([0, 1]),
            'is_nav' => fake()->randomElement([0, 1]),
            'index_position' => fake()->randomElement([0, 1, 2, 3]),
            'created_user' => fake()->randomNumber(),
            'updated_user' => fake()->randomNumber(),
        ];
    }

    public function published(): Factory
    {
        return $this->state(function () {
            return [
                'categories_status' => 1,
            ];
        });
    }

    public function unpublished(): Factory
    {
        return $this->state(function () {
            return [
                'categories_status' => 0,
            ];
        });
    }

    public function isAdvertorial(): Factory
    {
        return $this->state(function () {
            return [
                'categories_type' => MainCategoriesType::ADVERTORIAL,
            ];
        });
    }

    public function filterAdvertorial(): Factory
    {
        return $this->state(function () {
            return [
                'categories_type' => fake()->randomElement([1, 2, 4, 5]),
            ];
        });
    }
}
