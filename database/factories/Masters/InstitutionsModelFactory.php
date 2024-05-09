<?php

namespace Database\Factories\Masters;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class InstitutionsModelFactory
 * @package Database\Factories\Masters
 */
class InstitutionsModelFactory extends Factory
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
            'nick_name' => fake()->userName,
            'status' => fake()->randomElement([0, 1]),
            'created_user' => fake()->name,
            'updated_user' => fake()->name,
        ];
    }

    /**
     * @return InstitutionsModelFactory
     */
    public function published(): InstitutionsModelFactory
    {
        return $this->state(function () {
            return [
                'status' => 1,
            ];
        });
    }
}
