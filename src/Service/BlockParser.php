<?php

namespace App\Service;

use App\Entity\Block;
use App\Repository\BlockRepository;

class BlockParser
{
    /** @var BlockRepository */
    private $blockRepository;

    /** @var TransactionParser */
    private $transactionParser;

    public function __construct(BlockRepository $blockRepository, TransactionParser $transactionParser)
    {
        $this->blockRepository = $blockRepository;
        $this->transactionParser = $transactionParser;
    }

    public function parseRawBlock(?array $rawBlock): ?Block
    {
        if (is_null($rawBlock)) {
            return null;
        }

        $block = new Block($rawBlock['number']);
        $block->setBlockHash($rawBlock['hash']);
        $block->setGasLimit($rawBlock['gasLimit']);
        $block->setGasUsed($rawBlock['gasUsed']);
        $block->setPreviousBlockHash($rawBlock['parentHash']);
        $block->setSize($rawBlock['size']);
        $block->setTimestamp($rawBlock['timestamp']);

        $this->handlePreviousBlock($block);
        $this->blockRepository->save($block);

        $this->handleTransactions($rawBlock['transactions']);

        return $block;
    }

    private function handlePreviousBlock(Block &$block)
    {
        $previousBlock = $this->blockRepository->findByBlockHash($block->getPreviousBlockHash());

        $block->setPreviousBlock($previousBlock);
    }

    private function handleTransactions(array $rawTransactions)
    {
        /** @var array $rawTransaction */
        foreach ($rawTransactions as $rawTransaction) {
            $this->transactionParser->parseRawTransaction($rawTransaction);
        }
    }
}
