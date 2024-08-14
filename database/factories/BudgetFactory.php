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
        static $uniqueCombinations = [];

        do {
            $category_id = $this->faker->numberBetween(2, 21);
            $year = $this->faker->numberBetween(2020, 2024);
            $month = $this->faker->numberBetween(1, 12);
            $combination = $category_id.'-'.$year.'-'.$month;
        } while (in_array($combination, $uniqueCombinations, true));

        $uniqueCombinations[] = $combination;

        return [
            'budget' => $this->faker->optional()->randomNumber(),
            'year' => $year,
            'month' => $month,
            'team_id' => 1,
            'category_id' => $category_id,
        ];

    }
}
