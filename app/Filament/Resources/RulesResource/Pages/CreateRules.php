<?php

namespace App\Filament\Resources\RulesResource\Pages;

use App\Filament\Resources\RulesResource;
use App\Models\Amount;
use App\Models\Day;
use App\Models\Note;
use App\Models\PayeeName;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateRules extends CreateRecord
{
    protected static string $resource = RulesResource::class;

    protected function handleRecordCreation(array $data): Model
    {

        $transformedData = [];
        foreach ($data['if_actions'] as $index => $item) {
            $key = $item['if'];
            $transformedData[$key] = $item;
        }

        $transformedDataThen = [];
        foreach ($data['then_actions'] as $index => $item) {
            $key = $item['then'];
            $transformedDataThen[$key] = $item;
        }

        $categoryId = $transformedData['matches_category']['category'] ?? null;
        $inAccount = $transformedData['in_account']['type'] ?? null;
        $data['team_id'] = auth()->user()->teams[0]->id;
        $payeeNameId = null;
        $noteId = null;
        $amountId = null;
        $dayId = null;

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

        $record = $this->getModel()::create($data);
        $record->ifAction()->create([
            'matches_payee_name' => $payeeNameId,
            'matches_notes' => $noteId,
            'matches_amount' => $amountId,
            'matches_day' => $dayId,
            'matches_category' => $categoryId,
            'in_account' => $inAccount,
        ]);

        $thenTest = null;
        $addTag = $transformedDataThen['add_tags']['add_tags'] ?? null;

        $record->thenAction()->create([
            'set_payee' => $transformedDataThen['set_payee']['set_payee'] ?? null,
            'set_notes' => $transformedDataThen['set_notes']['set_notes'] ?? null,
            'set_category' => $transformedDataThen['set_category']['set_category'] ?? null,
            'set_uncategorized' => array_key_exists('set_uncategorized', $transformedDataThen),
            'add_tag' => $addTag,
            'delete_transaction' => $thenTest,
            'link_to_recurring_item' => $thenTest,
            'link_not_link_to_recurring_item' => $thenTest,
            'do_not_create_rule' => $thenTest,
            'split_transaction' => $thenTest,
            'mark_as_reviewed' => $thenTest,
            'mark_as_unreviewed' => $thenTest,
            'send_me_email' => $thenTest,
        ]);

        return $record;
    }
}
