<?php

namespace App\Service\Observer;

use App\Event\TransactionReceiptReceivedEvent;
use App\Repository\TransactionRepository;
use App\Service\CalculateBlockAverageGasPriceService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BlockGasPriceAndBlockRewardObserver implements EventSubscriberInterface
{
    /** @var TransactionRepository */
    private $transactionRepository;

    /** @var CalculateBlockAverageGasPriceService */
    private $calculateBlockAverageGasPriceService;

    public function __construct(
        TransactionRepository $transactionRepository,
        CalculateBlockAverageGasPriceService $calculateBlockAverageGasPriceService
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->calculateBlockAverageGasPriceService = $calculateBlockAverageGasPriceService;
    }

    public static function getSubscribedEvents()
    {
        return [
            TransactionReceiptReceivedEvent::class => 'updateBlockGasPriceAndBlockReward',
        ];
    }

    public function updateBlockGasPriceAndBlockReward(TransactionReceiptReceivedEvent $event)
    {
        $transaction = $this->transactionRepository->find($event->getTxHash());

        if (is_null($transaction)) {
            throw new \Exception('Transaction doesnt exist');
        }

        $this->calculateBlockAverageGasPriceService->calculateForBlock($transaction->getBlock());
    }
}
