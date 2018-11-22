<?php

namespace App\Service\TokenDetection;

use App\Entity\Contract;

class TokenDetectionService
{
    /** @var ERC20TokenDetectionService */
    private $erc20TokenDetectionService;

    /** @var ERC721TokenDetectionService */
    private $erc721TokenDetectionService;

    public function __construct(
        ERC20TokenDetectionService $erc20TokenDetectionService,
        ERC721TokenDetectionService $erc721TokenDetectionService
    ) {
        $this->erc20TokenDetectionService = $erc20TokenDetectionService;
        $this->erc721TokenDetectionService = $erc721TokenDetectionService;
    }

    public function detectTokens(Contract $contract)
    {
        $this->erc20TokenDetectionService->detectIsERC20Token($contract);
        $this->erc721TokenDetectionService->detectIsERC721Token($contract);
    }
}
