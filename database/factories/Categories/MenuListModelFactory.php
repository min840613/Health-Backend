<?php

namespace Database\Factories\Categories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class MenuListModelFactory
 * @package Database\Factories
 */
class MenuListModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'parentid' => 1,
            'title' => fake()->name,
            'url' => fake()->url(),
            'position' => 1,
            'categories_id' => 2,
            'blank' => 1,
            'menu_list_status' => 1,
            'is_app' => 1,
            'layout' => 1,
            'sort' => 1,
            'created_user' => 'test',
            'updated_user' => 'test',
        ];
    }

    public function isWeb(): MenuListModelFactory
    {
        return $this->state(function () {
            return [
                'is_app' => 0,
            ];
        });
    }
}
