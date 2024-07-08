<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Payee;
use App\Models\RecurringItem;
use App\Models\Tag;
use App\Models\Team;
use App\Models\TeamUser;
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
        User::create([
            'name' => 'usama',
            'email' => 'usama@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('usama@gmail.com'),
            'remember_token' => Str::random(10),
            'current_team_id' => null,
            'profile_photo_path' => null,
        ]);

        Team::create([
            'user_id' => '1',
            'name' => 'usama team',
            'personal_team' => 1,
        ]);

        TeamUser::create([
            'team_id' => 1,
            'user_id' => 1,
            'role' => 'super_admin',
        ]);

        Category::create([
            'name' => 'transport',
            'description' => 'every day transport',
            'budget' => '50',
            'treat_as_income' => false,
            'exclude_from_budget' => false,
            'exclude_from_total' => true,
            'team_id' => 1,
        ]);

        RecurringItem::create([
            'name' => 'recurring item 1',
            'billing_date' => 'every day',
            'repeating_cadence' => 'Once_a_week',
            'description' => Str::random(10),
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'team_id' => 1,
        ]);

        Tag::create([
            'name' => 'job',
            'description' => 'job expenses',
            'color' => '#457821',
            'team_id' => 1,
        ]);

        Payee::create([
            'name' => 'Usama',
        ]);

        Budget::create([
            'budget' => '500',
            'category_id' => 1,
            'team_id' => 1,
        ]);

        Account::create([
            'name' => 'account one',
            'balance' => '500',
            'status' => Status::TRUE,
            'user_id' => 1,
            'team_id' => 1,
            'category_id' => 1,
        ]);

        Transaction::create([
            'amount' => '50',
            'payee_id' => 1,
            'tag_id' => 1,
            'category_id' => 1,
            'account_id' => 1,
            'date' => now(),
            'notes' => Str::random(10),
            'transaction_source' => 'CSV import',
            'status' => true,
            'team_id' => 1,
        ]);
    }
}
