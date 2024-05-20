<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum IfActions: string
{
    use EnumToArray;
    case MATCHES_PAYEE_NAME = 'matches payee name';
    case MATCHES_CATEGORY = 'matches category';
    case MATCHES_NOTES = 'matches notes';
    case MATCHES_AMOUNT = 'matches amount';
    case BETWEEN_EXCLUSIVE = 'between exclusive';
    case MATCHES_DAY = 'matches day';
    case In_ACCOUNT = 'in account';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
