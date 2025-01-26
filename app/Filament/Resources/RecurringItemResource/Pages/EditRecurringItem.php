<?php

declare(strict_types=1);

namespace App\Filament\Resources\RecurringItemResource\Pages;

use App\Filament\Resources\RecurringItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRecurringItem extends EditRecord
{
    protected static string $resource = RecurringItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
