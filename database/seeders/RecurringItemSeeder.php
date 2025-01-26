<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\RecurringItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RecurringItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RecurringItem::create([
            'name' => 'recurring item 1',
            'billing_date' => 'every day',
            'repeating_cadence' => 'Once_a_week',
            'description' => Str::random(10),
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'team_id' => 1,
        ]);

        RecurringItem::factory()->count(20)->create();
    }
}
