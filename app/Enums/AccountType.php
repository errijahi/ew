<?php

namespace App\Enums;

enum AccountType: string
{
    case INCOME_CREDITS = 'income/credits';
    case EXPENSES_DEBITS = 'expenses/debits';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
