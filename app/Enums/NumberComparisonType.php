<?php

declare(strict_types=1);

namespace App\Enums;

enum NumberComparisonType: string
{
    case GREATER_THAN = 'greater_than';
    case GREATER_THAN_OR_EQUAL_TO = 'greater_than_or_equal_to';
    case LESS_THAN = 'less_than';
    case LESS_THAN_OR_EQUAL_TO = 'less_than_or_equal_to';
    case BETWEEN_EXCLUSIVE = 'between_exclusive';
    case EXACTLY = 'exactly';

    /** @return array<string, string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value', 'value');
    }
}
