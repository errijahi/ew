<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rule>
 */
class RuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => 1,
            'priority' => $this->faker->numberBetween(1, 20),
            'stop_processing_other_rules' => $this->faker->boolean(),
            'delete_this_rule_after_use' => $this->faker->boolean(),
            'rule_on_transaction_update' => $this->faker->boolean(),
        ];
    }
}
