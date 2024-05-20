<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum Days: int
{
    use EnumToArray;
    case D1 = 1;
    case D2 = 2;
    case D3 = 3;
    case D4 = 4;
    case D5 = 5;
    case D6 = 6;
    case D7 = 7;
    case D8 = 8;
    case D9 = 9;
    case D10 = 10;
    case D11 = 11;
    case D12 = 12;
    case D13 = 13;
    case D14 = 14;
    case D15 = 15;
    case D16 = 16;
    case D17 = 17;
    case D18 = 18;
    case D19 = 19;
    case D20 = 20;
    case D21 = 21;
    case D22 = 22;
    case D23 = 23;
    case D24 = 24;
    case D25 = 25;
    case D26 = 26;
    case D27 = 27;
    case D28 = 28;
    case D29 = 29;
    case D30 = 30;
    case D31 = 31;

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
