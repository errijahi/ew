<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Payee;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $teamId = auth()->user()->teams[0]->id;
        $data['team_id'] = $teamId;

        $payeeRecord = Payee::create(['name' => $data['payee']]);
        $transactionRecord = $this->getModel()::create($data);

        $transactionRecord->payee()->create([
            'payee_id' => $payeeRecord->id,
            'transaction_id' => $transactionRecord->id,
        ]);

        return $transactionRecord;
    }
}
