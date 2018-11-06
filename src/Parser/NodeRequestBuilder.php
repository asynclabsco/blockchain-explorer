<?php

namespace App\Parser;

use App\Parameters\NodeRpcUrlParameter;
use Datto\JsonRpc\Client as JsonRpcClient;
use DomainException;
use GuzzleHttp\Client;

class NodeRequestBuilder
{
    /** @var NodeRpcUrlParameter */
    private $nodeRpcUrlParameter;

    /** @var Client */
    private $client;

    /** @var JsonRpcClient */
    private $jsonRpcClient;

    public function __construct(NodeRpcUrlParameter $nodeRpcUrlParameter)
    {
        $this->nodeRpcUrlParameter = $nodeRpcUrlParameter;
        $this->client = new Client();
        $this->jsonRpcClient = new JsonRpcClient();
    }

    // TODO Tests
    public function executeSingleRequest(?string $validJsonRPCPayload): array
    {
        $res = $this->client->request('POST', $this->nodeRpcUrlParameter->getPath(), [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body'    => $validJsonRPCPayload,
        ]);

        $responseContent = $res->getBody()->getContents();

        if (!is_string($responseContent)) {
            throw new DomainException('Response is not JSON string');
        }

        $responseArray = $this->jsonRpcClient->decode($responseContent);

        if (!is_array($responseArray)) {
            throw new DomainException('Parsed response is not an array.');
        }

        return $responseArray;
    }
}
