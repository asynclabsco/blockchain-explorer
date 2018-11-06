<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Address
{
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
}
