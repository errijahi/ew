<?php

namespace App\Filament\Resources\NetWorthResource\Pages;

use App\Filament\Resources\NetWorthResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNetWorth extends EditRecord
{
    protected static string $resource = NetWorthResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
