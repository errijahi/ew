<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuperAdminPanelResource\Pages;

use App\Filament\Resources\SuperAdminPanelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSuperAdminPanel extends EditRecord
{
    protected static string $resource = SuperAdminPanelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
