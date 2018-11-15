<?php

namespace App\Service;

use App\Entity\Block;
use App\Entity\Blockchain;
use App\Enum\GethJsonRPCMethodsEnum;
use App\Parameters\AppParameters;
use App\Parser\NodeRequestBuilder;
use App\Repository\BlockchainRepository;
use App\Repository\BlockRepository;
use Datto\JsonRpc\Client as JsonRpcClient;
use Datto\JsonRpc\Response;
use DomainException;

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

    /** @var AppParameters */
    private $appParameters;

    /** @var BlockchainRepository */
    private $blockchainRepository;

    public function __construct(
        BlockRepository $blockRepository,
        NodeRequestBuilder $nodeRequestBuilder,
        BlockParser $blockParser,
        AppParameters $appParameters,
        BlockchainRepository $blockchainRepository
    ) {
        $this->blockRepository = $blockRepository;
        $this->nodeRequestBuilder = $nodeRequestBuilder;
        $this->blockParser = $blockParser;
        $this->appParameters = $appParameters;
        $this->blockchainRepository = $blockchainRepository;
        $this->jsonRpcClient = new JsonRpcClient;
    }

    public function fetchBlock()
    {
        $blockNumberDec = $this->getLatestBlockNumber();

        $rawBlocksArray = $this->getRawBlocksData($blockNumberDec);

        $newBlocksCount = 0;

        $blockchain = $this->blockchainRepository->getBlockchain();

        /** @var array $rawBlock */
        foreach ($rawBlocksArray as $rawBlock) {
            $newBlock = $this->blockParser->parseRawBlock($rawBlock);

            if (!($newBlock instanceof Block)) {
                continue;
            }

            $newBlocksCount++;
            $blockchain->setIndexedBlockHeight($newBlock->getBlockNumberDecimal());
        }

        $this->updateBlockchain($blockchain);

        $this->blockchainRepository->save($blockchain);

        return $newBlocksCount;
    }

    private function getLatestBlockNumber()
    {
        $latestBlock = $this->blockRepository->findLatestBlock();

        if (is_null($latestBlock)) {
            return $this->appParameters->getStartingBlockNumber();
        }

        $latestBlockNumberDec = NumberBaseConverter::toDec($latestBlock->getBlockNumber());

        return $latestBlockNumberDec;
    }

    private function getRawBlocksData($blockNumberDec)
    {
        $rawBlocksArray = [];

        for ($i = 1; $i <= $this->appParameters->getBlocksPerRequest(); $i++) {
            $this->jsonRpcClient->query($i, GethJsonRPCMethodsEnum::GET_BLOCK_BY_NUMBER,
                [NumberBaseConverter::toHex($blockNumberDec + $i), true]);
        }
        $message = $this->jsonRpcClient->encode();

        $responseArray = $this->nodeRequestBuilder->executeRequest($message);

        /** @var Response $response */
        foreach ($responseArray as $response) {
            $rawBlocksArray[] = $response->getResult();
        }

        return $rawBlocksArray;
    }

    private function updateBlockchain(Blockchain $blockchain)
    {
        $this->jsonRpcClient->query(1, GethJsonRPCMethodsEnum::GET_BLOCK_NUMBER, []);
        $message = $this->jsonRpcClient->encode();

        $responseArray = $this->nodeRequestBuilder->executeRequest($message);

        if (!is_array($responseArray)) {
            throw new DomainException('Response not received');
        }

        /** @var Response $blockNumberResponse */
        $blockNumberResponse = $responseArray[0];

        if (is_null($blockNumberResponse->getResult())) {
            throw new DomainException('Block number height not received');
        }

        $blockchain->setBlockchainBlockHeight(NumberBaseConverter::toDec($blockNumberResponse->getResult()));
    }
}
