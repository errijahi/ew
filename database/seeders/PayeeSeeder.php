<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Payee;
use Illuminate\Database\Seeder;

class PayeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Payee::create([
            'name' => 'Usama',
        ]);

        Payee::factory()->count(20)->create();
    }
}
