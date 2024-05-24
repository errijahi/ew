<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum Status: string
{
    use EnumToArray;
    case TRUE = 'true';
    case FALSE = 'false';

    public static function values(): array
    {
        return array_column(self::cases(), 'value', 'value');
    }
}
