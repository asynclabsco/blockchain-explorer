<?php

namespace App\Entity;

use App\Enum\AddressTypeEnum;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Address
{
    const NULL_ADDRESS = '0x0000000000000000000000000000000000000000';

    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(type="string", nullable=false)
     */
    private $address;

    /**
     * @var float
     * @ORM\Column(type="integer", nullable=false)
     */
    private $ethereumBalance = 0;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $type = AddressTypeEnum::WALLET;

    public function __construct(string $address)
    {
        $this->address = $address;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address)
    {
        $this->address = $address;
    }

    public function getEthereumBalance(): float
    {
        return $this->ethereumBalance;
    }

    public function setEthereumBalance(float $ethereumBalance)
    {
        $this->ethereumBalance = $ethereumBalance;
    }

    public function isNullAddress(): bool
    {
        return $this->address === self::NULL_ADDRESS;
    }

    public function isWalletAddress(): bool
    {
        return !$this->isNullAddress() && $this->type === AddressTypeEnum::WALLET;
    }

    public function isSmartContractAddress(): bool
    {
        return !$this->isNullAddress() && $this->type === AddressTypeEnum::SMART_CONTRACT;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;
    }

    public function markSmartContract()
    {
        $this->type = AddressTypeEnum::SMART_CONTRACT;
    }
}
