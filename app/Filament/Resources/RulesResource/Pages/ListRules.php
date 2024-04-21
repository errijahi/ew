<?php

namespace App\Filament\Resources\RulesResource\Pages;

use App\Filament\Resources\RulesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRules extends ListRecords
{
    protected static string $resource = RulesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
