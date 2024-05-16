<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum Priority: int
{
    use EnumToArray;
    case P1 = 1;
    case P2 = 2;
    case P3 = 3;
    case P4 = 4;
    case P5 = 5;
    case P6 = 6;
    case P7 = 7;
    case P8 = 8;
    case P9 = 9;
    case P10 = 10;
    case P11 = 11;
    case P12 = 12;
    case P13 = 13;
    case P14 = 14;
    case P15 = 15;
    case P16 = 16;
    case P17 = 17;
    case P18 = 18;
    case P19 = 19;
    case P20 = 20;

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
