<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum TextMatchType: string
{
    use EnumToArray;
    case CONTAIN = 'contain';
    case MATCH_EXACTLY = 'match exactly';
    case START_WITH = 'start with';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
