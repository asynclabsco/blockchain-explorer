<?php

namespace App\Service\TokenDetection;

use App\Entity\Contract;
use App\Enum\ERC20TokenContractCalls;
use App\Enum\ERCTokensEventHashes;
use App\Enum\GethJsonRPCMethodsEnum;
use App\Parser\NodeRequestBuilder;
use App\Repository\ContractRepository;
use App\Service\NumberBaseConverter;
use Datto\JsonRpc\Client as JsonRpcClient;
use Datto\JsonRpc\Response;

class ERC20TokenDetectionService
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

    public function detectIsERC20Token(Contract $contract)
    {
        $transferEventExists = $this->checkDoTransferEventsExist($contract);
        $contract->getErc20TokenValidation()->setTransferEventExists($transferEventExists);

        $this->getERC20TokenInformation($contract);

        $this->contractRepository->save($contract);
    }

    private function checkDoTransferEventsExist(Contract $contract)
    {
        $this->jsonRpcClient->query(1, GethJsonRPCMethodsEnum::GET_LOGS, [
            [
                'fromBlock' => NumberBaseConverter::toHex(0),
                'topics'    => [ERCTokensEventHashes::ERC20_TRANSFER],
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

    private function getERC20TokenInformation(Contract $contract)
    {
        $this->createAdditionalInformationRequest($contract, ERC20TokenContractCalls::GET_NAME);
        $this->createAdditionalInformationRequest($contract, ERC20TokenContractCalls::GET_SYMBOL);
        $this->createAdditionalInformationRequest($contract, ERC20TokenContractCalls::GET_TOTAL_SUPPLY);
        $this->createAdditionalInformationRequest($contract, ERC20TokenContractCalls::GET_DECIMALS);

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
                case ERC20TokenContractCalls::GET_NAME:
                    $name = $this->parseHexToAscii($response->getResult());
                    $contract->getErc20TokenValidation()->setName($name);
                    break;
                case ERC20TokenContractCalls::GET_DECIMALS:
                    $decimals = NumberBaseConverter::toDec($response->getResult());
                    $contract->getErc20TokenValidation()->setDecimals($decimals);
                    break;
                case ERC20TokenContractCalls::GET_TOTAL_SUPPLY:
                    $totalSupply = NumberBaseConverter::toDec($response->getResult());
                    $contract->getErc20TokenValidation()->setTotalSupply($totalSupply);
                    break;
                case ERC20TokenContractCalls::GET_SYMBOL:
                    $symbol = $this->parseHexToAscii($response->getResult());
                    $contract->getErc20TokenValidation()->setSymbol($symbol);
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
}
