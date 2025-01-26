<?php

declare(strict_types=1);

namespace App\Filament\Resources\OverviewResource\Pages;

use App\Filament\Resources\OverviewResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOverview extends EditRecord
{
    protected static string $resource = OverviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
