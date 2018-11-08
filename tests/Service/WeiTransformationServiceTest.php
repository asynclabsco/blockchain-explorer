<?php

namespace App\Tests\Service;

use App\Service\WeiTransformationService;
use PHPUnit\Framework\TestCase;

class WeiTransformationServiceTest extends TestCase
{
    public function data()
    {
        return [
            [5, 'wei', 5],
            [5 * 1000, 'Kwei', 5],
            [5 * 1000 * 1000, 'Mwei', 5],
            [5 * 1000 * 1000 * 1000, 'Gwei', 5],
            [5 * 1000 * 1000 * 1000 * 1000, 'microether', 5],
            [5 * 1000 * 1000 * 1000 * 1000 * 1000, 'milliether', 5],
            [5 * 1000 * 1000 * 1000 * 1000 * 1000 * 1000, 'ether', 5],
        ];
    }

    /**
     * @dataProvider data
     */
    public function testTransformations($inputWei, $transformTo, $expectedResult)
    {
        $value = WeiTransformationService::transformWei($inputWei, $transformTo);

        $this->assertEquals($expectedResult, $value);
    }
}
