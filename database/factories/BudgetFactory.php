<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'budget' => $this->faker->optional()->randomNumber(),
            'team_id' => 1,
            'category_id' => $this->faker->unique()->numberBetween(2, 21),
        ];
    }
}
