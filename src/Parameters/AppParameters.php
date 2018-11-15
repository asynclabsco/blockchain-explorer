<?php

namespace App\Parameters;

class AppParameters
{
    /** @var string */
    private $nodeRpcUrl;

    /** @var string */
    private $title;

    /** @var string */
    private $consensusProtocol;

    /** @var integer */
    private $blocksPerRequest;

    /** @var integer */
    private $startingBlockNumber;

    /** @var integer */
    private $timeoutBetweenRequests;

    public function __construct(
        string $nodeRpcUrl,
        string $title,
        string $consensusProtocol,
        int $blocksPerRequest,
        int $startingBlockNumber,
        int $timeoutBetweenRequests
    ) {
        $this->nodeRpcUrl = $nodeRpcUrl;
        $this->title = $title;
        $this->consensusProtocol = $consensusProtocol;
        $this->blocksPerRequest = $blocksPerRequest;
        $this->startingBlockNumber = $startingBlockNumber;
        $this->timeoutBetweenRequests = $timeoutBetweenRequests;
    }

    public function getNodeRpcUrl(): string
    {
        return $this->nodeRpcUrl;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getConsensusProtocol(): string
    {
        return $this->consensusProtocol;
    }

    public function getBlocksPerRequest(): int
    {
        return $this->blocksPerRequest;
    }

    public function getStartingBlockNumber(): int
    {
        return $this->startingBlockNumber;
    }

    public function getTimeoutBetweenRequests(): int
    {
        return $this->timeoutBetweenRequests;
    }
}
