<?php

namespace Database\Factories\Articles;

use App\Enums\SponsorAdCategoriesType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class SponsorAdModelFactory
 * @package Database\Factories
 */
class SponsorAdModelFactory extends Factory
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
            'categories_type' => fake()->randomElement([1, 2]),
            'categories_list_id' => fake()->randomNumber(),
            'position' => fake()->randomNumber(),
            'start' => fake()->dateTime,
            'end' => fake()->dateTime,
            'created_user' => fake()->randomNumber(),
            'updated_user' => fake()->randomNumber(),
        ];
    }

    public function isMainCategory(): Factory
    {
        return $this->state(function () {
            return [
                'categories_type' => SponsorAdCategoriesType::MAIN,
            ];
        });
    }

    public function isSubCategory(): Factory
    {
        return $this->state(function () {
            return [
                'categories_type' => SponsorAdCategoriesType::SUB,
            ];
        });
    }

    public function active(): Factory
    {
        return $this->state(function () {
            return [
                'start' => now()->subDay(),
                'end' => now()->addDay(),
            ];
        });
    }
}
