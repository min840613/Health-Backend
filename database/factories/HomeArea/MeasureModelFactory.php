<?php

namespace Database\Factories\HomeArea;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KeywordClickCountModel>
 */
class MeasureModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        return [
            'title' => fake()->randomElement(['A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7', 'A8', 'A9', 'A10']),
            'link' => fake()->url(),
            'image' => fake()->imageUrl(),
            'start' => date('Y-m-d H:i:s', strtotime('-10 sec')),
            'end' => date('Y-m-d H:i:s', strtotime('+10 day')),
            'status' => fake()->randomElement([0, 1]),
            'sort' => fake()->unique()->randomDigit(),
            'created_user' => 'test',
        ];
    }

    public function active($status = 1)
    {
        return $this->state(fn (array $attributes) => ['status' => $status]);
    }

}
