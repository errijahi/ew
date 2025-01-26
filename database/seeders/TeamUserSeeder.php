<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\TeamUser;
use Illuminate\Database\Seeder;

class TeamUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TeamUser::create([
            'team_id' => 1,
            'user_id' => 1,
            'role' => 'super_admin',
        ]);
    }
}
