<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KeywordClickCountModel>
 */
class HealthExhibitionListModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        return [
            'title' => fake()->randomElement(['A1', 'A2', 'A3', 'A4', 'A5', 'A6']),
            'ip' => 4,
            'image' => fake()->imageUrl(),
            'web_url' => fake()->url(),
            'app_url' => fake()->url(),
            'blank' => fake()->randomElement(['0', '1']),
            'start_at' => date('Y-m-d H:i:s', strtotime('-10 sec')),
            'end_at' => date('Y-m-d H:i:s', strtotime('+10 day')),
            'sort' => '1',
        ];
    }

    public function expired()
    {
        return $this->state(fn (array $attributes) => [
            'start_at' => date('Y-m-d H:i:s', strtotime('+10 sec')),
            'end_at'   => date('Y-m-d H:i:s', strtotime('-10 sec')),
        ]);
    }

}
