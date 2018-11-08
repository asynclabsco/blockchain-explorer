<?php

namespace App\Service;

class WeiTransformationService
{
    const WEI = 'wei';
    const KWEI = 'Kwei';
    const MWEI = 'Mwei';
    const GWEI = 'Gwei';
    const MICROETHER = 'microether';
    const MILLIETHER = 'milliether';
    const ETHER = 'ether';

    public static function transformWei($wei, string $transformTo = self::WEI)
    {
        switch ($transformTo) {
            case self::WEI:
                return $wei;
                break;
            case self::KWEI:
                return $wei / pow(10, 3);
                break;
            case self::MWEI:
                return $wei / pow(10, 6);
                break;
            case self::GWEI:
                return $wei / pow(10, 9);
                break;
            case self::MICROETHER:
                return $wei / pow(10, 12);
                break;
            case self::MILLIETHER:
                return $wei / pow(10, 15);
                break;
            case self::ETHER:
                return $wei / pow(10, 18);
                break;
            default:
                throw new \DomainException('You need to set denomination to something valid.');
        }
    }
}
