<?php

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
            'category_id' => 1,
            'team_id' => 1,
        ]);

        Budget::factory()->count(20)->create();
    }
}
