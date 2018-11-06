<?php

namespace App\Tests\Service;

use App\Service\NumberBaseConverter;
use PHPUnit\Framework\TestCase;


class NumberBaseConverterTest extends TestCase
{
    public function data()
    {
        return [
            ['0x0', 0],
            ['0x1', 1],
            ['0x8', 8],
            ['0x100000000', 4294967296],
            ['0x10000000000000', 4503599627370496],
        ];
    }

    /**
     * @dataProvider data
     */
    public function testConvertingHexToDecimal($hexadecimal, $decimal)
    {
        $this->assertEquals($hexadecimal, NumberBaseConverter::toHex($decimal));
    }

    /**
     * @dataProvider data
     */
    public function testConvertingDecimalToHex($hexadecimal, $decimal)
    {
        $this->assertEquals($decimal, NumberBaseConverter::toDec($hexadecimal));
    }
}
