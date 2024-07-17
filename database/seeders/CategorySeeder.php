<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'name' => 'transport',
            'description' => 'every day transport',
            'budget' => '50',
            'treat_as_income' => false,
            'exclude_from_budget' => false,
            'exclude_from_total' => true,
            'team_id' => 1,
        ]);

        Category::factory()->count(20)->create();
    }
}
