<?php

namespace App\Console;

use App\Service\FetchLatestBlockFromBlockchainService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchBlocksConsoleCommand extends Command
{
    /** @var FetchLatestBlockFromBlockchainService */
    private $fetchLatestBlockFromBlockchainService;

    public function __construct(FetchLatestBlockFromBlockchainService $fetchLatestBlockFromBlockchainService)
    {
        $this->fetchLatestBlockFromBlockchainService = $fetchLatestBlockFromBlockchainService;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('blockchain:fetch:blocks');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Started fetching blocks');

        while (true) {
            $numberOfNewBlocks = $this->fetchLatestBlockFromBlockchainService->fetchBlock();

            $output->writeln("Got {$numberOfNewBlocks} new blocks.");
        }

        $output->writeln('Finished fetching blocks');
    }
}
