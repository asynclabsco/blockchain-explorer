<?php

namespace App\Service;

use App\Entity\Address;
use App\Enum\GethJsonRPCMethodsEnum;
use App\Parser\NodeRequestBuilder;
use App\Repository\AddressRepository;
use Datto\JsonRpc\Client;
use Datto\JsonRpc\Response;

class AddressFinderService
{
    /** @var AddressRepository */
    private $addressRepository;

    /** @var NodeRequestBuilder */
    private $nodeRequestBuilder;

    /** @var Client */
    private $jsonRpcClient;

    public function __construct(AddressRepository $addressRepository, NodeRequestBuilder $nodeRequestBuilder)
    {
        $this->addressRepository = $addressRepository;
        $this->nodeRequestBuilder = $nodeRequestBuilder;
        $this->jsonRpcClient = new Client();
    }

    public function findOrCreateAddress(string $address): Address
    {
        $addressDb = $this->addressRepository->find($address);

        if (is_null($addressDb)) {
            $addressDb = $this->createAddress($address);
        }

        $this->addressRepository->save($addressDb);

        return $addressDb;
    }

    private function createAddress(string $address): Address
    {
        $addressDb = new Address($address);

        $this->jsonRpcClient->query($address, GethJsonRPCMethodsEnum::GET_BALANCE, [$address, '0x0']);
        $request = $this->jsonRpcClient->encode();
        $responses = $this->nodeRequestBuilder->executeRequest($request);

        /** @var Response $response */
        foreach ($responses as $response) {
            if ($response->getId() === $address) {
                $addressDb->setEthereumBalance($response->getResult());
            }
        }

        return $addressDb;
    }
}
