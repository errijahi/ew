<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word.' tag',
            'description' => $this->faker->text(50),
            'color' => $this->faker->safeHexColor,
            'team_id' => 1,
        ];
    }
}
