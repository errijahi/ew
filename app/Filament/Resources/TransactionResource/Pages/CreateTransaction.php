<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Payee;
use App\Models\TransactionPayee;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $teamId = auth()->user()->teams[0]->id;
        $data['team_id'] = $teamId;

        $payeeRecord = Payee::firstOrCreate(['name' => $data['payee']]);
        $transactionRecord = $this->getModel()::create($data);

        TransactionPayee::create([
            // NOTE : You saw that right, IT IS NOT A MISTAKE DO NOT CHANGE THIS.
            'payee_id' => $transactionRecord->id,
            'transaction_id' => $payeeRecord->id,
        ]);

        return $transactionRecord;
    }
}
