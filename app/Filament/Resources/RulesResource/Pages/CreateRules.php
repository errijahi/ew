<?php

namespace App\Filament\Resources\RulesResource\Pages;

use App\Filament\Resources\RulesResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use App\Models\PayeeName;
use App\Models\Category;
use App\Models\Note;
use App\Models\Amount;
use App\Models\Day;
use App\Models\AccountType;

class CreateRules extends CreateRecord
{
    protected static string $resource = RulesResource::class;

    protected function handleRecordCreation(array $data): Model
    {
//        dd($data);
        $transformedData = [];
        foreach ($data['if_actions'] as $index => $item) {
            $key = $item['if'];
            $transformedData[$key] = $item;
        }

        $data['team_id'] = auth()->user()->teams[0]->id;

        array_key_exists('matches_payee_name', $transformedData) ? PayeeName::create($transformedData['matches_payee_name']) : null;
        array_key_exists('matches_notes', $transformedData) ? Note::create($transformedData['matches_notes']) : null;
        array_key_exists('matches_amount', $transformedData) ? Amount::create($transformedData['matches_amount']) : null;
        array_key_exists('matches_day', $transformedData) ? Day::create($transformedData['matches_day']) : null;

        dd(PayeeName::class);

        $record = $this->getModel()::create($data);
        $record->ifAction()->create([
            'matches_payee_name' => 1,
            'matches_category' => 2,
        ]);

        return $record;
    }
}
