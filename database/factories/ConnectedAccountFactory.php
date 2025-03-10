<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ConnectedAccount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use JoelButcher\Socialstream\Providers;

/**
 * @extends Factory<ConnectedAccount>
 */
class ConnectedAccountFactory extends Factory
{
    protected $model = ConnectedAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'provider' => $this->faker->randomElement(Providers::all()),
            'provider_id' => $this->faker->numerify('########'),
            'token' => Str::random(432),
            'refresh_token' => Str::random(432),
        ];
    }
}
