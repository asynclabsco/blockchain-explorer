<?php

namespace App\Service\TokenDetection;

use App\Entity\Contract;

class TokenDetectionService
{
    /** @var ERC20TokenDetectionService */
    private $erc20TokenDetectionService;

    public function __construct(ERC20TokenDetectionService $erc20TokenDetectionService)
    {
        $this->erc20TokenDetectionService = $erc20TokenDetectionService;
    }

    public function detectTokens(Contract $contract)
    {
        $this->erc20TokenDetectionService->detectIsERC20Token($contract);
    }
}
