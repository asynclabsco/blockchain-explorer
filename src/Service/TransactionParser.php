<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Repository\BlockRepository;
use App\Repository\TransactionRepository;
use DomainException;

class TransactionParser
{
    /** @var TransactionRepository */
    private $transactionRepository;

    /** @var BlockRepository */
    private $blockRepository;

    /** @var AddressFinderService */
    private $addressFinderService;

    public function __construct(
        TransactionRepository $transactionRepository,
        BlockRepository $blockRepository,
        AddressFinderService $addressFinderService
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->blockRepository = $blockRepository;
        $this->addressFinderService = $addressFinderService;
    }

    public function parseRawTransaction(array $rawTransaction)
    {
        $transaction = new Transaction();
        $transaction->setTxHash($rawTransaction['hash']);
        $transaction->setGasLimit($rawTransaction['gas']);
        $transaction->setGasPrice($rawTransaction['gasPrice']);
        $transaction->setNonce($rawTransaction['nonce']);
        $transaction->setValue($rawTransaction['value']);
        $transaction->setData($rawTransaction['input']);
        $transaction->setIndex($rawTransaction['transactionIndex']);

        // Set Block
        $block = $this->blockRepository->findByBlockHash($rawTransaction['blockHash']);
        if (is_null($block)) {
            throw new DomainException('Something is wrong.');
        }
        $transaction->setBlock($block);

        $this->handleAddresses($transaction, $rawTransaction);

        $this->transactionRepository->save($transaction);

        return $transaction;
    }

    private function handleAddresses(Transaction &$transaction, array $rawTransaction)
    {
        $fromAddress = $this->addressFinderService->findOrCreateAddress($rawTransaction['from']);
        $transaction->setFrom($fromAddress);

        $toAddress = $this->addressFinderService->findOrCreateAddress($rawTransaction['to']);
        $transaction->setTo($toAddress);
    }

}
