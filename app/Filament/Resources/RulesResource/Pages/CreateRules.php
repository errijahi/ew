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
//       dd($data['if_actions']);

        $transformedData = [];
        foreach ($data['if_actions'] as $index => $item) {
            $key = $item['if'];
            $transformedData[$key] = $item;
        }

//     dd($transformedData);

        $data['team_id'] = auth()->user()->teams[0]->id;

        $payeeNameId = null;
        $noteId = null;
        $amountId = null;
        $dayId = null;
        $categoryId = $transformedData['matches_category']['category'] ?? null;
//        $inAccount = $transformedData['in_account']['type'] ?? null;

        if (array_key_exists('matches_payee_name', $transformedData)) {
            $payeeName = PayeeName::create($transformedData['matches_payee_name']);
            $payeeNameId = $payeeName->id;
        }

        if (array_key_exists('matches_notes', $transformedData)) {
            $note = Note::create($transformedData['matches_notes']);
            $noteId = $note->id;
        }

        if (array_key_exists('matches_amount', $transformedData)) {
            $amount = Amount::create($transformedData['matches_amount']);
            $amountId = $amount->id;
        }

        if (array_key_exists('matches_day', $transformedData)) {
            $day = Day::create($transformedData['matches_day']);
            $dayId = $day->id;
        }

//        dd($dayId);

        $record = $this->getModel()::create($data);
        $record->ifAction()->create([
            'matches_payee_name' => $payeeNameId,
            'matches_notes' => $noteId,
            'matches_amount' => $amountId,
            'matches_day' => $dayId,
            'matches_category' => $categoryId,
//            'in_account' => $inAccount
        ]);

        return $record;
    }
}
