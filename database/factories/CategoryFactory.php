<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word,
            'description' => $this->faker->text(50),
            'treat_as_income' => $this->faker->boolean,
            'exclude_from_budget' => $this->faker->boolean,
            'exclude_from_total' => $this->faker->boolean,
            'budget' => $this->faker->randomNumber(),
            'team_id' => 1,
        ];
    }
}
