<?php

namespace App\Service;

use App\Entity\Block;
use App\Entity\Transaction;
use App\Repository\BlockRepository;
use App\Repository\TransactionRepository;

class CalculateBlockAverageGasPriceService
{
    /** @var BlockRepository */
    private $blockRepository;

    /** @var TransactionRepository */
    private $transactionRepository;

    public function __construct(BlockRepository $blockRepository, TransactionRepository $transactionRepository)
    {
        $this->blockRepository = $blockRepository;
        $this->transactionRepository = $transactionRepository;
    }

    public function calculateForBlock(Block $block)
    {
        // TODO Calculate block reward at some point
        $transactions = $this->transactionRepository->findSuccessfullTransactionsForBlock($block);

        $totalGasPrice = 0;

        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            $totalGasPrice += $transaction->getGasPrice();
        }

        $averageGasPrice = $totalGasPrice / $block->getNumberOfTransactions();

        $block->setAverageGasPrice($averageGasPrice);
        $this->blockRepository->save($block);
    }
}
