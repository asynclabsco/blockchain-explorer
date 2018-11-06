<?php

namespace App\Entity;

use App\Service\NumberBaseConverter;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Transaction
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(type="string", nullable=false)
     */
    private $txHash;

    /**
     * @var Block
     * @ORM\ManyToOne(targetEntity="App\Entity\Block")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="id")
     */
    private $block;

    /**
     * @var Address
     * @ORM\ManyToOne(targetEntity="App\Entity\Address", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="address")
     */
    private $from;

    /**
     * @var Address
     * @ORM\ManyToOne(targetEntity="App\Entity\Address", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="address")
     */
    private $to;

    /**
     * @var int
     * @ORM\Column(type="bigint", nullable=false)
     */
    private $gas;

    /**
     * @var int
     * @ORM\Column(type="bigint", nullable=false)
     */
    private $gasPrice;

    /**
     * @var int
     * @ORM\Column(type="bigint", nullable=false)
     */
    private $nonce;

    /**
     * This is ETH sent with transaction
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $value;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $data;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=false)
     */
    private $index;

    public function getTxHash(): string
    {
        return $this->txHash;
    }

    public function setTxHash(string $txHash)
    {
        $this->txHash = $txHash;
    }

    public function getBlock(): Block
    {
        return $this->block;
    }

    public function setBlock(Block $block)
    {
        $this->block = $block;
    }

    public function getFrom(): Address
    {
        return $this->from;
    }

    public function setFrom(Address $from)
    {
        $this->from = $from;
    }

    public function getTo(): Address
    {
        return $this->to;
    }

    public function setTo(Address $to)
    {
        $this->to = $to;
    }

    public function getGas(): int
    {
        return $this->gas;
    }

    public function setGas($gas)
    {
        if (is_string($gas)) {
            $gas = NumberBaseConverter::toDec($gas);
        }

        $this->gas = $gas;
    }

    public function getGasPrice(): int
    {
        return $this->gasPrice;
    }

    public function setGasPrice($gasPrice)
    {
        if (is_string($gasPrice)) {
            $gasPrice = NumberBaseConverter::toDec($gasPrice);
        }

        $this->gasPrice = $gasPrice;
    }

    public function getNonce(): int
    {
        return $this->nonce;
    }

    public function setNonce($nonce)
    {
        if (is_string($nonce)) {
            $nonce = NumberBaseConverter::toDec($nonce);
        }

        $this->nonce = $nonce;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue($value)
    {
        if (is_string($value)) {
            $value = NumberBaseConverter::toDec($value);
        }

        $this->value = $value;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function setData(string $data)
    {
        $this->data = $data;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function setIndex($index)
    {
        if (is_string($index)) {
            $index = NumberBaseConverter::toDec($index);
        }
        $this->index = $index;
    }
}
