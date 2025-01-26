<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Budget;
use Illuminate\Database\Seeder;

class BudgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Budget::create([
            'budget' => '500',
            'year' => '2024',
            'month' => '04',
            'category_id' => 1,
            'team_id' => 1,
        ]);

        Budget::factory()->count(20)->create();
    }
}
