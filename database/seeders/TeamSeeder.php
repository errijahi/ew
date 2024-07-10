<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Team::create([
            'user_id' => '1',
            'name' => 'usama team',
            'personal_team' => 1,
        ]);
    }
}
