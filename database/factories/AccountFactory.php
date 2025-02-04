<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Status;
use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
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
        $status = $this->faker->randomElement(Status::cases());
        return [
            'name' => $this->faker->unique()->company.' account',
            'balance' => $this->faker->randomNumber(),
            'status' => $status instanceof Status ? $status->value : Status::cases()[0]->value, // Ensuring a valid enum value
            'category_id' => $this->faker->numberBetween(1, 20),
        ];
    }
}
