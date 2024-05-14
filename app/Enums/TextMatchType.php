<?php

namespace App\Enums;

enum TextMatchType: string
{
    case CONTAIN = 'contain';
    case MATCH_EXACTLY = 'match_exactly';
    case START_WITH = 'start_with';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
