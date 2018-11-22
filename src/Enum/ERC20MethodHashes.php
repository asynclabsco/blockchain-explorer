<?php

namespace App\Enum;

use ReflectionClass;

class ERC20MethodHashes
{
    // name()
    const GET_NAME = '0x06fdde03';

    // totalSupply()
    const GET_TOTAL_SUPPLY = '0x18160ddd';

    // decimals()
    const GET_DECIMALS = '0x313ce567';

    // symbol()
    const GET_SYMBOL = '0x95d89b41';

    /**
     * Interface method hashes without prefix
     */
    const INTERFACE_TOTAL_SUPPLY = '18160ddd';
    const BALANCE_OF = '70a08231';
    const ALLOWANCE = 'dd62ed3e';
    const TRANSFER = 'a9059cbb';
    const APPROVE = '095ea7b3';
    const TRANSFER_FROM = '23b872dd';

    public static function getConstants()
    {
        $oClass = new ReflectionClass(static::class);

        $constants = $oClass->getConstants();
        unset($constants['GET_NAME']);
        unset($constants['GET_TOTAL_SUPPLY']);
        unset($constants['GET_DECIMALS']);
        unset($constants['GET_SYMBOL']);

        return $constants;
    }
}
