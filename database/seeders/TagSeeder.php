<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tag::create([
            'name' => 'job',
            'description' => 'job expenses',
            'color' => '#457821',
            'team_id' => 1,
        ]);

        Tag::factory()->count(20)->create();
    }
}
