<?php

namespace App\Event;

use Symfony\Component\EventDispatcher\Event;

class TransactionReceiptReceivedEvent extends Event
{
    /** @var string */
    private $txHash;

    public function __construct(string $txHash)
    {
        $this->txHash = $txHash;
    }

    public function getTxHash(): string
    {
        return $this->txHash;
    }
}
