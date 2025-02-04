<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\EnumToArray;

enum TextMatchType: string
{
    use EnumToArray;
    case CONTAIN = 'contain';
    case MATCH_EXACTLY = 'match_exactly';
    case START_WITH = 'start_with';

    /** @return array<string, string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value', 'value');
    }
}
