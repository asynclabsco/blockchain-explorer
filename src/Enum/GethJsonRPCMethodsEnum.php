<?php

namespace App\Enum;

class GethJsonRPCMethodsEnum
{
    const GET_TRANSACTION_RECEIPT = 'eth_getTransactionReceipt';
    const GET_BLOCK_BY_NUMBER = 'eth_getBlockByNumber';
    const GET_BALANCE = 'eth_getBalance';
    const GET_BLOCK_NUMBER = 'eth_blockNumber';
    const GET_LOGS = 'eth_getLogs';
    const GET_WEB3_SHA3 = 'web3_sha3';
    const ETH_CALL_CONTRACT = 'eth_call';
}
