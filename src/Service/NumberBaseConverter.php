<?php

namespace App\Service;

class NumberBaseConverter
{
    public static function toHex($decimalNumber)
    {
        return "0x" . dechex($decimalNumber);
    }

    public static function toDec($hexadecimalNumber): string
    {
        return number_format(hexdec($hexadecimalNumber), null, '', '');
    }
}
