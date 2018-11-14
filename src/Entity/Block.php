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
     * @ORM\Column(type="string", nullable=false)
     */
    private $difficulty;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, length=1000)
     */
    private $extraData;

    /**
     * @var int
     * @ORM\Column(type="string", nullable=false)
     */
    private $gasLimit;

    /**
     * @var integer
     * @ORM\Column(type="string", nullable=false)
     */
    private $gasUsed;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $averageGasPrice = 0;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $blockHash;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, length=514)
     */
    private $logsBloom;

    /**
     * @var Address
     * @ORM\ManyToOne(targetEntity="App\Entity\Address", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="address")
     */
    private $miner;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, length=500))
     */
    private $mixHash;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $nonce;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, name="block_number")
     */
    private $blockNumber;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, name="block_number_decimal")
     */
    private $blockNumberDecimal;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $parentBlockHash;

    /**
     * @var null|Block
     * @ORM\OneToOne(targetEntity="App\Entity\Block")
     * @ORM\JoinColumn(nullable=true, referencedColumnName="id")
     */
    private $parentBlock;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $receiptsRoot;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $sha3Uncles;

    /**
     * @var int
     * @ORM\Column(type="string", nullable=false)
     */
    private $size;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $stateRoot;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $timestamp;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $totalDifficulty;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $numberOfTransactions;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $transactionsRoot;

    public function __construct(string $blockNumber)
    {
        $this->setBlockNumber($blockNumber);
        $this->id = NumberBaseConverter::toDec($blockNumber);
    }

    public function getBlockNumber(): string
    {
        return $this->blockNumber;
    }

    public function setBlockNumber(string $blockNumber)
    {
        $this->blockNumber = $blockNumber;
        $this->blockNumberDecimal = NumberBaseConverter::toDec($blockNumber);
    }

    public function getBlockHash(): string
    {
        return $this->blockHash;
    }

    public function setBlockHash(string $blockHash)
    {
        $this->blockHash = $blockHash;
    }

    public function getParentBlockHash(): string
    {
        return $this->parentBlockHash;
    }

    public function setParentBlockHash(string $parentBlockHash)
    {
        $this->parentBlockHash = $parentBlockHash;
    }

    public function getParentBlock(): ?Block
    {
        return $this->parentBlock;
    }

    public function setParentBlock(?Block $parentBlock)
    {
        $this->parentBlock = $parentBlock;
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

    public function getGasUsedPercentage()
    {
        return ($this->gasUsed / $this->gasLimit) * 100;
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

    public function getId(): int
    {
        return $this->id;
    }

    public function getNumberOfTransactions(): int
    {
        return $this->numberOfTransactions;
    }

    public function setNumberOfTransactions(int $numberOfTransactions)
    {
        $this->numberOfTransactions = $numberOfTransactions;
    }

    public function getDifficulty(): string
    {
        return $this->difficulty;
    }

    public function setDifficulty(string $difficulty)
    {
        $this->difficulty = $difficulty;
    }

    public function getExtraData(): string
    {
        return $this->extraData;
    }

    public function setExtraData(string $extraData)
    {
        $this->extraData = $extraData;
    }

    public function getLogsBloom(): string
    {
        return $this->logsBloom;
    }

    public function setLogsBloom(string $logsBloom)
    {
        $this->logsBloom = $logsBloom;
    }

    public function getMiner(): Address
    {
        return $this->miner;
    }

    public function setMiner(Address $miner)
    {
        $this->miner = $miner;
    }

    public function getMixHash(): string
    {
        return $this->mixHash;
    }

    public function setMixHash(string $mixHash)
    {
        $this->mixHash = $mixHash;
    }

    public function getNonce(): string
    {
        return $this->nonce;
    }

    public function setNonce(string $nonce)
    {
        $this->nonce = $nonce;
    }

    public function getReceiptsRoot(): string
    {
        return $this->receiptsRoot;
    }

    public function setReceiptsRoot(string $receiptsRoot)
    {
        $this->receiptsRoot = $receiptsRoot;
    }

    public function getSha3Uncles(): string
    {
        return $this->sha3Uncles;
    }

    public function setSha3Uncles(string $sha3Uncles)
    {
        $this->sha3Uncles = $sha3Uncles;
    }

    public function getStateRoot(): string
    {
        return $this->stateRoot;
    }

    public function setStateRoot(string $stateRoot)
    {
        $this->stateRoot = $stateRoot;
    }

    public function getTotalDifficulty(): string
    {
        return $this->totalDifficulty;
    }

    public function setTotalDifficulty(string $totalDifficulty)
    {
        $this->totalDifficulty = $totalDifficulty;
    }

    public function getTransactionsRoot(): string
    {
        return $this->transactionsRoot;
    }

    public function setTransactionsRoot(string $transactionsRoot)
    {
        $this->transactionsRoot = $transactionsRoot;
    }

    public function getAverageGasPrice(): string
    {
        return $this->averageGasPrice;
    }

    public function setAverageGasPrice($averageGasPrice)
    {
        $this->averageGasPrice = $averageGasPrice;
    }

    public function getBlockNumberDecimal(): string
    {
        return $this->blockNumberDecimal;
    }
}
