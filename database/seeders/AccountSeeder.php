<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Account::create([
            'name' => 'account one',
            'balance' => '500',
            'status' => Status::TRUE,
            'user_id' => 1,
            'team_id' => 1,
            'category_id' => 1,
        ]);
    }
}
