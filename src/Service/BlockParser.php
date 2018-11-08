<?php

namespace App\Service;

use App\Entity\Block;
use App\Repository\AddressRepository;
use App\Repository\BlockRepository;

class BlockParser
{
    /** @var BlockRepository */
    private $blockRepository;

    /** @var TransactionParser */
    private $transactionParser;

    /** @var AddressRepository */
    private $addressRepository;

    public function __construct(
        BlockRepository $blockRepository,
        TransactionParser $transactionParser,
        AddressRepository $addressRepository
    ) {
        $this->blockRepository = $blockRepository;
        $this->transactionParser = $transactionParser;
        $this->addressRepository = $addressRepository;
    }

    public function parseRawBlock(?array $rawBlock): ?Block
    {
        if (is_null($rawBlock)) {
            return null;
        }

        $block = new Block($rawBlock['number']);
        $block->setDifficulty($rawBlock['difficulty']);
        $block->setExtraData($rawBlock['extraData']);
        $block->setGasLimit($rawBlock['gasLimit']);
        $block->setGasUsed($rawBlock['gasUsed']);
        $block->setBlockHash($rawBlock['hash']);
        $block->setLogsBloom($rawBlock['logsBloom']);

        $minerAddress = $this->addressRepository->findOrCreateAddress($rawBlock['miner']);
        $block->setMiner($minerAddress);

        $block->setMixHash($rawBlock['mixHash']);
        $block->setNonce($rawBlock['nonce']);
        $block->setParentBlockHash($rawBlock['parentHash']);
        $block->setReceiptsRoot($rawBlock['receiptsRoot']);
        $block->setSha3Uncles($rawBlock['sha3Uncles']);
        $block->setSize($rawBlock['size']);

        $block->setStateRoot($rawBlock['stateRoot']);
        $block->setTimestamp($rawBlock['timestamp']);
        $block->setTotalDifficulty($rawBlock['totalDifficulty']);
        $block->setTransactionsRoot($rawBlock['transactionsRoot']);
        $block->setNumberOfTransactions(count($rawBlock['transactions']));

        // TODO uncles

        $this->handlePreviousBlock($block);
        $this->blockRepository->save($block);

        $this->handleTransactions($rawBlock['transactions']);

        return $block;
    }

    private function handlePreviousBlock(Block &$block)
    {
        $previousBlock = $this->blockRepository->findByBlockHash($block->getParentBlockHash());

        $block->setParentBlock($previousBlock);
    }

    private function handleTransactions(array $rawTransactions)
    {
        /** @var array $rawTransaction */
        foreach ($rawTransactions as $rawTransaction) {
            $this->transactionParser->parseRawTransaction($rawTransaction);
        }
    }
}
