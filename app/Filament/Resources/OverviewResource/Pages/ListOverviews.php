<?php

namespace App\Filament\Resources\OverviewResource\Pages;

use App\Filament\Resources\OverviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOverviews extends ListRecords
{
    protected static string $resource = OverviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
