<?php

declare(strict_types=1);

namespace App\Filament\Resources\OverviewResource\Pages;

use App\Filament\Resources\OverviewResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOverview extends CreateRecord
{
    protected static string $resource = OverviewResource::class;
}
