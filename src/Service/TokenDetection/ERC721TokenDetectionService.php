<?php

namespace App\Service\TokenDetection;

use App\Entity\Contract;
use App\Entity\Model\ERC721Token;
use App\Enum\ERC721MethodHashes;
use App\Enum\ERCTokensEventHashes;
use App\Enum\GethJsonRPCMethodsEnum;
use App\Parser\NodeRequestBuilder;
use App\Repository\ContractRepository;
use App\Service\NumberBaseConverter;
use Datto\JsonRpc\Client as JsonRpcClient;
use Datto\JsonRpc\Response;

class ERC721TokenDetectionService
{
    /** @var NodeRequestBuilder */
    private $nodeRequestBuilder;

    /** @var ContractRepository */
    private $contractRepository;

    /** @var JsonRpcClient */
    private $jsonRpcClient;

    public function __construct(NodeRequestBuilder $nodeRequestBuilder, ContractRepository $contractRepository)
    {
        $this->nodeRequestBuilder = $nodeRequestBuilder;
        $this->contractRepository = $contractRepository;
        $this->jsonRpcClient = new JsonRpcClient();
    }

    public function detectIsERC721Token(Contract $contract)
    {
        $erc721Token = $contract->getErc721Token();

        if (is_null($erc721Token)) {
            $erc721Token = new ERC721Token($contract);
        }

        $transferEventExists = $this->checkDoTransferEventsExist($contract);
        $erc721Token->setTransferEventExists($transferEventExists);

        $this->getERC721TokenInformation($erc721Token);
        $this->checkDoInterfaceMethodsExist($erc721Token);

        if ($erc721Token->isErc721Token()) {
            $contract->setErc721Token($erc721Token);
        }

        $this->contractRepository->save($contract);
    }

    private function checkDoTransferEventsExist(Contract $contract)
    {
        $this->jsonRpcClient->query(1, GethJsonRPCMethodsEnum::GET_LOGS, [
            [
                'fromBlock' => NumberBaseConverter::toHex(0),
                'topics'    => [ERCTokensEventHashes::ERC721_TRANSFER],
                'address'   => $contract->getAddress()->getAddress(),
            ],
        ]);

        $message = $this->jsonRpcClient->encode();

        $response = $this->nodeRequestBuilder->executeRequest($message);

        if (!is_array($response)) {
            return false;
        }

        /** @var Response $singleResponse */
        $singleResponse = $response[0];
        $result = $singleResponse->getResult();

        if (!is_array($result) || count($result) < 1) {
            return false;
        }

        return true;
    }

    private function getERC721TokenInformation(ERC721Token $token)
    {
        $contract = $token->getContract();

        $this->createAdditionalInformationRequest($contract, ERC721MethodHashes::GET_NAME);
        $this->createAdditionalInformationRequest($contract, ERC721MethodHashes::GET_SYMBOL);
        $this->createAdditionalInformationRequest($contract, ERC721MethodHashes::GET_TOTAL_SUPPLY);

        $message = $this->jsonRpcClient->encode();

        $responses = $this->nodeRequestBuilder->executeRequest($message);

        if (!is_array($responses)) {
            throw new \DomainException('Response is not array');
        }

        /** @var Response $response */
        foreach ($responses as $response) {
            if ($response->isError()) {
                continue;
            }

            switch ($response->getId()) {
                case ERC721MethodHashes::GET_NAME:
                    $name = $this->parseHexToAscii($response->getResult());
                    $token->setName($name);
                    break;
                case ERC721MethodHashes::GET_TOTAL_SUPPLY:
                    $totalSupply = NumberBaseConverter::toDec($response->getResult());
                    $token->setTotalSupply($totalSupply);
                    break;
                case ERC721MethodHashes::GET_SYMBOL:
                    $symbol = $this->parseHexToAscii($response->getResult());
                    $token->setSymbol($symbol);
                    break;
            }
        }
    }

    private function createAdditionalInformationRequest(Contract $contract, string $additionalInfoString)
    {
        $this->jsonRpcClient->query($additionalInfoString, GethJsonRPCMethodsEnum::ETH_CALL_CONTRACT, [
            [
                'to'   => $contract->getAddress()->getAddress(),
                'data' => $additionalInfoString,
            ],
            'latest',
        ]);
    }

    private function parseHexToAscii(string $hexString)
    {
        $value = str_replace('0x', '', $hexString);

        $value = hex2bin($value);

        $value = preg_replace('/[^a-zA-Z0-9_ -]/s', '', $value);

        return trim($value);
    }

    private function checkDoInterfaceMethodsExist(ERC721Token $erc721Token)
    {
        $byteCode = $erc721Token->getContract()->getByteCode();

        $constants = ERC721MethodHashes::getConstants();

        $foundMethods = [];
        $notFoundMethods = [];

        foreach ($constants as $name => $hash) {
            $position = strpos($byteCode, $hash);

            $position === false ? $notFoundMethods[] = $name : $foundMethods[] = $name;
        }

        $erc721Token->setFoundInterfaceMethods($foundMethods);
        $erc721Token->setNotFoundInterfaceMethods($notFoundMethods);
    }
}
