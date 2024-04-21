<?php

namespace App\Filament\Resources\TrendsResource\Pages;

use App\Filament\Resources\TrendsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrends extends ListRecords
{
    protected static string $resource = TrendsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
