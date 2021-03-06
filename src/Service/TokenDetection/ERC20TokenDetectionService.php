<?php

namespace App\Service\TokenDetection;

use App\Entity\Contract;
use App\Entity\Model\ERC20Token;
use App\Enum\ERC20MethodHashes;
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
        $erc20Token = $contract->getErc20Token();

        if (is_null($erc20Token)) {
            $erc20Token = new ERC20Token($contract);
        }

        $transferEventExists = $this->checkDoTransferEventsExist($contract);
        $erc20Token->setTransferEventExists($transferEventExists);

        $this->getERC20TokenInformation($erc20Token);
        $this->checkDoInterfaceMethodsExist($erc20Token);

        if ($erc20Token->isERC20Token()) {
            $contract->setErc20Token($erc20Token);
        }

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

    private function getERC20TokenInformation(ERC20Token $token)
    {
        $contract = $token->getContract();

        $this->createAdditionalInformationRequest($contract, ERC20MethodHashes::GET_NAME);
        $this->createAdditionalInformationRequest($contract, ERC20MethodHashes::GET_SYMBOL);
        $this->createAdditionalInformationRequest($contract, ERC20MethodHashes::GET_TOTAL_SUPPLY);
        $this->createAdditionalInformationRequest($contract, ERC20MethodHashes::GET_DECIMALS);

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
                case ERC20MethodHashes::GET_NAME:
                    $name = $this->parseHexToAscii($response->getResult());
                    $token->setName($name);
                    break;
                case ERC20MethodHashes::GET_DECIMALS:
                    $decimals = NumberBaseConverter::toDec($response->getResult());
                    $token->setDecimals($decimals);
                    break;
                case ERC20MethodHashes::GET_TOTAL_SUPPLY:
                    $totalSupply = NumberBaseConverter::toDec($response->getResult());
                    $token->setTotalSupply($totalSupply);
                    break;
                case ERC20MethodHashes::GET_SYMBOL:
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

    private function checkDoInterfaceMethodsExist(ERC20Token $erc20Token)
    {

        $byteCode = $erc20Token->getContract()->getByteCode();

        $constants = ERC20MethodHashes::getConstants();

        $foundMethods = [];
        $notFoundMethods = [];

        foreach ($constants as $name => $hash) {
            $position = strpos($byteCode, $hash);

            $position === false ? $notFoundMethods[] = $name : $foundMethods[] = $name;
        }

        $erc20Token->setFoundInterfaceMethods($foundMethods);
        $erc20Token->setNotFoundInterfaceMethods($notFoundMethods);
    }
}
