<?php

namespace App\Filament\Resources\RecurringItemResource\Pages;

use App\Filament\Resources\RecurringItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRecurringItems extends ListRecords
{
    protected static string $resource = RecurringItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
