<?php

declare(strict_types=1);

namespace App\Filament\Resources\AnalyzeResource\Pages;

use App\Filament\Resources\AnalyzeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAnalyze extends EditRecord
{
    protected static string $resource = AnalyzeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
