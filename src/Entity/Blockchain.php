<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Blockchain
{
    /**
     * @var integer
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", nullable=false, unique=true)
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="decimal", precision=50, scale=0, nullable=false)
     */
    private $blockchainBlockHeight = 0;

    /**
     * @var string
     * @ORM\Column(type="decimal", precision=50, scale=0, nullable=false)
     */
    private $indexedBlockHeight = 0;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getBlockchainBlockHeight(): string
    {
        return $this->blockchainBlockHeight;
    }

    public function setBlockchainBlockHeight(string $blockchainBlockHeight)
    {
        $this->blockchainBlockHeight = $blockchainBlockHeight;
    }

    public function getIndexedBlockHeight(): string
    {
        return $this->indexedBlockHeight;
    }

    public function setIndexedBlockHeight(string $indexedBlockHeight)
    {
        $this->indexedBlockHeight = $indexedBlockHeight;
    }

    public function getPercentageIndexed(): string
    {
        $percentageIndexed = 0;

        if ($this->blockchainBlockHeight > 0) {
            $percentageIndexed = ($this->indexedBlockHeight / $this->blockchainBlockHeight) * 100;
        }

        return number_format($percentageIndexed, 2, ',', '');
    }

    public function isIndexed(): bool
    {
        return $this->blockchainBlockHeight === $this->indexedBlockHeight;
    }

    public function isNotIndexed(): bool
    {
        return !$this->isIndexed();
    }
}
