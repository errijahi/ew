<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\IfAction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IfAction>
 */

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\IfAction>
 */
class IfActionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rule_id' => $this->faker->unique()->numberBetween(2, 20),
            'category_id' => $this->faker->numberBetween(1, 20),
        ];
    }
}
