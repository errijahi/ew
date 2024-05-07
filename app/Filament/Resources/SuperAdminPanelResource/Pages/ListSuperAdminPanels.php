<?php

namespace App\Filament\Resources\SuperAdminPanelResource\Pages;

use App\Filament\Resources\SuperAdminPanelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuperAdminPanels extends ListRecords
{
    protected static string $resource = SuperAdminPanelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
