<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Transaction::create([
            'amount' => '50',
            'payee_id' => 1,
            'tag_id' => 1,
            'category_id' => 1,
            'account_id' => 1,
            'notes' => Str::random(10),
            'transaction_source' => 'CSV import',
            'status' => true,
            'team_id' => 1,
        ]);

        Transaction::factory()->count(20)->create();
    }
}
