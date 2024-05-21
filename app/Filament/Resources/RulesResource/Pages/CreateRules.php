<?php

namespace App\Filament\Resources\RulesResource\Pages;

use App\Filament\Resources\RulesResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateRules extends CreateRecord
{
    protected static string $resource = RulesResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        //        dd('tito');
        $record = static::getModel()::create($data);
        $data['rule_id'] = 1;
        $payee = 1;
        dd($record->ifAction());
        $record->ifAction()->create([
            'rule_id' => 1,
            'matches_payee_name' => 1,
            'matches_category' => 2,
        ]);

        return $record;
    }
}
