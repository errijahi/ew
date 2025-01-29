<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ThenAction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ThenAction>
 */
class ThenActionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rule_id' => $this->faker->numberBetween(1, 20),
            'tag_id' => $this->faker->optional()->numberBetween(1, 20),
        ];
    }
}
