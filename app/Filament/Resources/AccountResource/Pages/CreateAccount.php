<?php

namespace App\Filament\Resources\AccountResource\Pages;

use App\Filament\Resources\AccountResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAccount extends CreateRecord
{
    protected static string $resource = AccountResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data['user_id'] = auth()->user()->id;
        $data['team_id'] = auth()->user()->teams[0]->id;

        return $this->getModel()::create($data);
    }
}
