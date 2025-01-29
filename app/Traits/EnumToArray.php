<?php

declare(strict_types=1);

namespace App\Traits;

trait EnumToArray
{
    /** @return string[] */
    public static function names(): array
    {
        return array_column(static::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(static::cases(), 'value');
    }

    /** @return string[] */
    public static function array(): array
    {
        return array_combine(static::values(), static::names());
    }
}
