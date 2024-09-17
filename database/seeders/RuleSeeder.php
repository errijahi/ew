<?php

namespace Database\Seeders;

use App\Models\IfAction;
use App\Models\Rule;
use App\Models\ThenAction;
use Illuminate\Database\Seeder;

class RuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Rule::create([
            'team_id' => 1,
            'priority' => 10,
            'stop_processing_other_rules' => false,
            'delete_this_rule_after_use' => false,
            'rule_on_transaction_update' => true,
        ]);

        IfAction::create([
            'rule_id' => 1,
            'category_id' => 1,
        ]);

        ThenAction::create([
            'rule_id' => 1,
            'tag_id' => 1,
        ]);

        Rule::factory()->count(20)->create();
        IfAction::factory()->count(19)->create();
        ThenAction::factory()->count(20)->create();
    }
}
