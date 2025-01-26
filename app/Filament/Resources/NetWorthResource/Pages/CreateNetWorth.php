<?php

declare(strict_types=1);

namespace App\Filament\Resources\NetWorthResource\Pages;

use App\Filament\Resources\NetWorthResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNetWorth extends CreateRecord
{
    protected static string $resource = NetWorthResource::class;
}
