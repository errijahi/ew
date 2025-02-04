<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\EnumToArray;

enum Cadence: string
{
    use EnumToArray;
    case ONCE_A_WEEK = 'Once_a_week';
    case TWICE_A_MONTH = 'Twice_a_month';
    case EVERY_2_WEEKS = 'Every_2_weeks';
    case MONTHLY = 'Monthly';
    case EVERY_2_MONTHS = 'Every_2_months';
    case EVERY_3_MONTHS = 'Every_3_months';
    case EVERY_4_MONTHS = 'Every_4_months';
    case TWICE_A_YEAR = 'Twice_a_year';
    case YEARLY = 'Yearly';

    /** @return array<string, string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value', 'value');
    }
}
