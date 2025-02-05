<?php

declare(strict_types=1);

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\PayeeTransaction;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $teamId = auth()->user()?->teams[0]?->id;
        $data['team_id'] = $teamId;
        $transactionRecord = $this->getModel()::create($data);

        PayeeTransaction::create([
            'payee_id' => $data['payee_id'],
            'transaction_id' => $transactionRecord->id,
        ]);

        return $transactionRecord;
    }
}
