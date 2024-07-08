<?php

namespace App\Filament\Resources\RulesResource\Pages;

use App\Filament\Resources\RulesResource;
use App\Models\Amount;
use App\Models\Day;
use App\Models\Note;
use App\Models\PayeeName;
use App\Models\SplitTransaction;
use App\Models\RuleSplitTransaction;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateRules extends CreateRecord
{
    protected static string $resource = RulesResource::class;

    protected function handleRecordCreation(array $data): Model
    {

        //TODO: add DB::beginTransaction(); DB::commit(); I probably need it?
        $teamId = auth()->user()->teams[0]->id;
        $data['team_id'] = $teamId;

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

        $record->thenAction()->create([
            'set_payee' => $transformedDataThen['set_payee']['set_payee'] ?? null,
            'set_notes' => $transformedDataThen['set_notes']['set_notes'] ?? null,
            'set_category' => $transformedDataThen['set_category']['set_category'] ?? null,
            'set_uncategorized' => array_key_exists('set_uncategorized', $transformedDataThen),
            'add_tag' => $transformedDataThen['add_tags']['add_tags'] ?? null,
            'delete_transaction' => array_key_exists('delete_transaction', $transformedDataThen),
            'link_to_recurring_item' => $transformedDataThen['link_to_recurring_item']['link_to_recurring_item'] ?? null,
            'do_not_link_to_recurring_item' => array_key_exists('do_not_link_to_recurring_item', $transformedDataThen),
            'do_not_create_rule' => array_key_exists('do_not_create_rule', $transformedDataThen),
            'split_transaction' => null, //TODO: later maybe I need to create a new table or something,right now too complex
            'mark_as_reviewed' => array_key_exists('mark_as_reviewed', $transformedDataThen),
            'mark_as_unreviewed' => array_key_exists('mark_as_unreviewed', $transformedDataThen),
            'send_me_email' => array_key_exists('send_me_email', $transformedDataThen),
        ]);

        $splitTransactions = [];

        if (array_key_exists('split_transaction', $transformedDataThen)) {
            foreach ($transformedDataThen['split_transaction']['split_transaction_repeater'] as $splitTransaction) {
                $splitTransactions[] = [
                    'amount' => $splitTransaction['amount_percentages'],
                    'payee' => $splitTransaction['set_payee_split_transaction'] ?? null,
                    'notes' => $splitTransaction['set_note_split_transaction'] ?? null,
                    'team_id' => $teamId,
                    'category_id' => $splitTransaction['set_category_split_transaction'] ?? null,
                    'tag_id' => $splitTransaction['set_tag_split_transaction'] ?? null,
                ];
            }

            SplitTransaction::insert($splitTransactions);
            $insertedIds = SplitTransaction::orderBy('id', 'desc')->take(count($splitTransactions))->pluck('id')->toArray();

            $pivotData = [];
            foreach ($insertedIds as $id) {
                $pivotData[] = [
                    'split_transaction_id' => $id,
                    'rule_id' => $record->id,
                ];
            }

            RuleSplitTransaction::insert($pivotData);
        }

        return $record;
    }
}
