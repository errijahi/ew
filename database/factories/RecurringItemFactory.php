<?php

namespace Database\Factories;

use App\Enums\Cadence;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecurringItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 month', '+1 month');
        $endDate = $this->faker->dateTimeBetween($startDate, '+1 year');

        return [
            'name' => $this->faker->unique()->word.' recurring item',
            'repeating_cadence' => Cadence::ONCE_A_WEEK,
            'description' => 'Recurring item description '.$this->faker->text(50),
            'billing_date' => 'every day',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'team_id' => 1,
        ];
    }
}
