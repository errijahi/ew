<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Team;
use App\Models\TeamUser;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
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
        ]);

        Transaction::create([
            'amount' => '50',
            'payee' => 'boris',
            'date' => now(),
            'notes' => Str::random(10),
            'transaction_source' => 'CSV import',
            'status' => true,
        ]);

        Account::create([
            'account_name' => 'transaction one',
            'status' => false,
            'balance' => '50',
        ]);

        Tag::create([
            'name' => 'job',
            'description' => 'job expenses',
            'color' => '#457821',
        ]);

        User::create([
            'name' => 'boris',
            'email' => 'boris@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('boris123'),
            'remember_token' => Str::random(10),
            'current_team_id' => null,
            'profile_photo_path' => null,
        ]);

        Team::create([
            'user_id' => '1',
            'name' => 'boris team',
            'personal_team' => 1,
        ]);

        TeamUser::create([
            'team_id' => 1,
            'user_id' => 1,
            'role' => 'super_admin',
        ]);
    }
}
