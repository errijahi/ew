<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\EnumToArray;

enum AccountType: string
{
    use EnumToArray;
    case INCOME_CREDITS = 'income/credits';
    case EXPENSES_DEBITS = 'expenses/debits';

    /** @return array<string, string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value', 'value');
    }
}
