<?php

namespace App\Filament\Resources\TrendsResource\Pages;

use App\Filament\Resources\TrendsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrends extends EditRecord
{
    protected static string $resource = TrendsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
