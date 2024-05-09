<?php

namespace Database\Factories\Articles;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class KeyvisualModelFactory
 * @package Database\Factories
 */
class KeyvisualModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'source_id' => fake()->randomNumber(),
            'type' => fake()->realText,
            'title' => fake()->name,
            'link' => fake()->url,
            'image' => fake()->imageUrl,
            'app_image' => fake()->imageUrl,
            'start' => fake()->dateTime,
            'end' => fake()->dateTime,
            'status' => fake()->randomElement([0, 1]),
            'sort' => fake()->randomNumber(),
            'created_user' => fake()->randomNumber(),
            'updated_user' => fake()->randomNumber(),
        ];
    }

    public function published(): Factory
    {
        return $this->state(function () {
            return [
                'status' => 1,
                'start' => now()->subDay()->toDateTimeString(),
                'end' => now()->addDay()->toDateTimeString(),
            ];
        });
    }

    public function unpublished(): Factory
    {
        return $this->state(function () {
            return [
                'status' => 0,
            ];
        });
    }
}
