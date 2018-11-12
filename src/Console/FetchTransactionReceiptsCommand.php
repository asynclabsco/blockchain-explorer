<?php

namespace App\Console;

use App\Service\FetchTransactionReceiptsService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchTransactionReceiptsCommand extends Command
{
    /** @var FetchTransactionReceiptsService */
    private $fetchTransactionReceiptsService;

    public function __construct(FetchTransactionReceiptsService $fetchTransactionReceiptsService)
    {
        $this->fetchTransactionReceiptsService = $fetchTransactionReceiptsService;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('blockchain:fetch:transaction-receipts');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->fetchTransactionReceiptsService->fetchTransactionReceipts();
    }
}
