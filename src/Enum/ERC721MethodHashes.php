<?php

namespace App\Enum;

use ReflectionClass;

class ERC721MethodHashes
{
    // name()
    const GET_NAME = '0x06fdde03';

    // totalSupply()
    const GET_TOTAL_SUPPLY = '0x18160ddd';

    // symbol()
    const GET_SYMBOL = '0x95d89b41';

    /**
     * Interface method hashes without prefix
     */
    const BALANCE_OF = '70a08231';
    const OWNER_OF = '6352211e';
    const SAFE_TRANSFER_FROM_WITH_BYTES = '63dbf371';
    const SAFE_TRANSFER_FROM = '405b1d72';
    const TRANSFER_FROM = '8d076e85';
    const APPROVE = '095ea7b3';
    const SET_APPROVAL_FOR_ALL = 'a22cb465';
    const GET_APPROVED = '081812fc';
    const IS_APPROVED_FOR_ALL = 'e985e9c5';

    public static function getConstants()
    {
        $oClass = new ReflectionClass(static::class);

        $constants = $oClass->getConstants();
        unset($constants['GET_NAME']);
        unset($constants['GET_TOTAL_SUPPLY']);
        unset($constants['GET_SYMBOL']);

        return $constants;
    }
}
