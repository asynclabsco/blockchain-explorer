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
     * @var int
     * @ORM\Column(type="bigint", nullable=false)
     */
    private $gasLimit;

    /**
     * @var int
     * @ORM\Column(type="bigint", nullable=false)
     */
    private $gasPrice;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, length=500000)
     */
    private $data;

    /**
     * @var int
     * @ORM\Column(type="bigint", nullable=false)
     */
    private $nonce;

    /**
     * @var Address
     * @ORM\ManyToOne(targetEntity="App\Entity\Address", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="address")
     */
    private $to;

    /**
     * @var Address
     * @ORM\ManyToOne(targetEntity="App\Entity\Address", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="address")
     */
    private $from;

    /**
     * This is ETH sent with transaction
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $value;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=false)
     */
    private $index;

    /**
     * @var null|bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $status;

    /**
     * @var null|string
     * @ORM\Column(type="string", nullable=true)
     */
    private $gasUsed;

    /**
     * @var null|Address
     * @ORM\ManyToOne(targetEntity="App\Entity\Address", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="address", nullable=true)
     */
    private $contractAddress;

    /**
     * @var null|string
     * @ORM\Column(type="string", nullable=true, length=1000)
     */
    private $logsBloom;

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
        // To address can be empty if it's contract creation
        if (is_null($this->to)) {
            return new Address(Address::NULL_ADDRESS);
        }

        return $this->to;
    }

    public function setTo(Address $to)
    {
        $this->to = $to;
    }

    public function getGasLimit(): int
    {
        return $this->gasLimit;
    }

    public function setGasLimit($gasLimit)
    {
        if (is_string($gasLimit)) {
            $gasLimit = NumberBaseConverter::toDec($gasLimit);
        }

        $this->gasLimit = $gasLimit;
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

    public function getGasUsed(): ?string
    {
        return $this->gasUsed;
    }

    public function setGasUsed(?string $gasUsed)
    {
        if (is_string($gasUsed)) {
            $gasUsed = NumberBaseConverter::toDec($gasUsed);
        }

        $this->gasUsed = $gasUsed;
    }

    public function getActualTxFee()
    {
        if (is_null($this->gasUsed)) {
            return 0;
        }

        return $this->gasUsed * $this->gasPrice;
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

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status)
    {
        $this->status = $status;
    }

    public function hasReceipt(): bool
    {
        return !is_null($this->status);
    }

    public function isSuccessful(): bool
    {
        return $this->hasReceipt() && $this->status;
    }

    public function isFailed(): bool
    {
        return $this->hasReceipt() && !$this->status;
    }

    public function isWaitingForReceipt(): bool
    {
        return !$this->hasReceipt();
    }

    public function getContractAddress(): ?Address
    {
        return $this->contractAddress;
    }

    public function setContractAddress(?Address $contractAddress)
    {
        $this->contractAddress = $contractAddress;
    }

    public function isContractCreationTransaction(): bool
    {
        return !is_null($this->contractAddress);
    }

    public function getLogsBloom(): ?string
    {
        return $this->logsBloom;
    }

    public function setLogsBloom(?string $logsBloom)
    {
        $this->logsBloom = $logsBloom;
    }
}
