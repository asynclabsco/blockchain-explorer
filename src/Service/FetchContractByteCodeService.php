<?php

namespace App\Service;

use App\Entity\Contract;
use App\Enum\GethJsonRPCMethodsEnum;
use App\Parser\NodeRequestBuilder;
use App\Repository\ContractRepository;
use Datto\JsonRpc\Client;
use Datto\JsonRpc\Response;

class FetchContractByteCodeService
{
    /** @var NodeRequestBuilder */
    private $nodeRequestBuilder;

    /** @var ContractRepository */
    private $contractRepository;

    /** @var Client */
    private $jsonRpcClient;

    public function __construct(NodeRequestBuilder $nodeRequestBuilder, ContractRepository $contractRepository)
    {
        $this->nodeRequestBuilder = $nodeRequestBuilder;
        $this->contractRepository = $contractRepository;
        $this->jsonRpcClient = new Client();
    }

    public function getContractByteCode(Contract $contract)
    {
        $this->jsonRpcClient->query($contract->getAddress()->getAddress(), GethJsonRPCMethodsEnum::GET_CODE, [
            $contract->getAddress()->getAddress(),
            'latest',
        ]);

        $message = $this->jsonRpcClient->encode();

        $responses = $this->nodeRequestBuilder->executeRequest($message);

        /** @var Response $response */
        foreach ($responses as $response) {
            if ($response->isError() || $contract->getAddress()->getAddress() !== $response->getId()) {
                continue;
            }

            $contract->setByteCode($response->getResult());
        }

        $this->contractRepository->save($contract);
    }
}
