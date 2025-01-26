<?php

declare(strict_types=1);

namespace App\Filament\Resources\NetWorthResource\Pages;

use App\Filament\Resources\NetWorthResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNetWorths extends ListRecords
{
    protected static string $resource = NetWorthResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
