<?php

declare(strict_types=1);

namespace App\Filament\Resources\RecurringItemResource\Pages;

use App\Filament\Resources\RecurringItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRecurringItem extends CreateRecord
{
    protected static string $resource = RecurringItemResource::class;
}
