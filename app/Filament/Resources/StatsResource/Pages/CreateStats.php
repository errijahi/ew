<?php

declare(strict_types=1);

namespace App\Filament\Resources\StatsResource\Pages;

use App\Filament\Resources\StatsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStats extends CreateRecord
{
    protected static string $resource = StatsResource::class;
}
