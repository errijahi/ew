<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(TeamSeeder::class);
        $this->call(TeamUserSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(RecurringItemSeeder::class);
        $this->call(TagSeeder::class);
        $this->call(PayeeSeeder::class);
        $this->call(BudgetSeeder::class);
        $this->call(AccountSeeder::class);
        $this->call(TransactionSeeder::class);
        $this->call(RuleSeeder::class);
    }
}
