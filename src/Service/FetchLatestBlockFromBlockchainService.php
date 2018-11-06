<?php

namespace App\Service;

use App\Parser\NodeRequestBuilder;
use App\Repository\BlockRepository;
use Datto\JsonRpc\Client as JsonRpcClient;
use Datto\JsonRpc\Response;

class FetchLatestBlockFromBlockchainService
{
    /** @var BlockRepository */
    private $blockRepository;

    /** @var NodeRequestBuilder */
    private $nodeRequestBuilder;

    /** @var JsonRpcClient */
    private $jsonRpcClient;

    /** @var BlockParser */
    private $blockParser;

    public function __construct(
        BlockRepository $blockRepository,
        NodeRequestBuilder $nodeRequestBuilder,
        BlockParser $blockParser
    ) {
        $this->blockRepository = $blockRepository;
        $this->nodeRequestBuilder = $nodeRequestBuilder;
        $this->blockParser = $blockParser;
        $this->jsonRpcClient = new JsonRpcClient;
    }


    public function fetchBlock()
    {
        $blockNumberHex = $this->getLatestBlockNumber();

        $rawBlocksArray = $this->getRawBlockData($blockNumberHex);

        /** @var array $rawBlock */
        foreach ($rawBlocksArray as $rawBlock) {
            $this->blockParser->parseRawBlock($rawBlock);
        }
    }

    private function getLatestBlockNumber()
    {
        $latestBlock = $this->blockRepository->findLatestBlock();

        if (is_null($latestBlock)) {
            return '0x0';
        }

        $latestBlockNumberDec = NumberBaseConverter::toDec($latestBlock->getBlockNumber());

        return NumberBaseConverter::toHex($latestBlockNumberDec + 1);
    }

    private function getRawBlockData(string $blockNumberHex)
    {
        $rawBlocksArray = [];

        $this->jsonRpcClient->query(1, 'eth_getBlockByNumber', [$blockNumberHex, true]);
        $message = $this->jsonRpcClient->encode();

        $responseArray = $this->nodeRequestBuilder->executeSingleRequest($message);

        /** @var Response $response */
        foreach ($responseArray as $response) {
            $rawBlocksArray[] = $response->getResult();
        }

        return $rawBlocksArray;
    }
}
