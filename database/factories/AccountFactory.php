<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Status;
use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company.' account',
            'balance' => $this->faker->randomNumber(),
            'status' => $this->faker->randomElement(Status::cases())->value,
            'team_id' => 1,
            'category_id' => $this->faker->numberBetween(1, 20),
        ];
    }
}
