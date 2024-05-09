<?php

namespace Database\Factories\Deepq;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class QuestionModel
 */
class QuestionModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'question' => fake()->text(10),
            'sort' => fake()->numerify,
        ];
    }
}
