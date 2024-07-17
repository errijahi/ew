<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        return [
            'amount' => $this->faker->randomNumber(),
            'payee_id' => $this->faker->numberBetween(1, 20),
            'tag_id' => $this->faker->optional()->numberBetween(1, 20),
            'category_id' => $this->faker->optional()->numberBetween(1, 20),
            'team_id' => 1,
            'account_id' => $this->faker->numberBetween(1, 20),
            'notes' => $this->faker->sentence,
            'transaction_source' => $this->faker->sentence,
            'status' => $this->faker->boolean,
            'created_at' => $this->faker->dateTimeBetween('2021-01-01', '2024-12-31'),
        ];
    }
}
