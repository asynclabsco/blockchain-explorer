<?php

namespace App\Entity;

use App\Service\NumberBaseConverter;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Block
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", nullable=false, unique=true)
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, name="block_number")
     */
    private $blockNumber;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $blockHash;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $previousBlockHash;

    /**
     * @var null|Block
     * @ORM\OneToOne(targetEntity="App\Entity\Block")
     * @ORM\JoinColumn(nullable=true, referencedColumnName="id")
     */
    private $previousBlock;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=false)
     */
    private $gasUsed;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $gasLimit;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $size;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $timestamp;

    public function __construct(string $blockNumber)
    {
        $this->blockNumber = $blockNumber;
        $this->id = NumberBaseConverter::toDec($blockNumber);
    }

    public function getBlockNumber(): string
    {
        return $this->blockNumber;
    }

    public function setBlockNumber(string $blockNumber)
    {
        $this->blockNumber = $blockNumber;
    }

    public function getBlockHash(): string
    {
        return $this->blockHash;
    }

    public function setBlockHash(string $blockHash)
    {
        $this->blockHash = $blockHash;
    }

    public function getPreviousBlockHash(): string
    {
        return $this->previousBlockHash;
    }

    public function setPreviousBlockHash(string $previousBlockHash)
    {
        $this->previousBlockHash = $previousBlockHash;
    }

    public function getPreviousBlock(): ?Block
    {
        return $this->previousBlock;
    }

    public function setPreviousBlock(?Block $previousBlock)
    {
        $this->previousBlock = $previousBlock;
    }

    public function getGasUsed(): int
    {
        return $this->gasUsed;
    }

    public function setGasUsed($gasUsed)
    {
        if (is_string($gasUsed)) {
            $gasUsed = NumberBaseConverter::toDec($gasUsed);
        }

        $this->gasUsed = $gasUsed;
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

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize($size)
    {
        if (is_string($size)) {
            $size = NumberBaseConverter::toDec($size);
        }

        $this->size = $size;
    }

    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    public function setTimestamp($timestamp)
    {
        if (is_string($timestamp)) {
            $timestamp = NumberBaseConverter::toDec($timestamp);
        }

        $this->timestamp = DateTime::createFromFormat('U', $timestamp);
    }
}
