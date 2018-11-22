<?php

namespace App\Entity;

use App\Entity\Model\ERC20Token;
use App\Entity\Model\ERC721Token;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Contract
{
    /**
     * @var integer
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", unique=true, nullable=false)
     */
    private $id;

    /**
     * @var Address
     * @ORM\ManyToOne(targetEntity="App\Entity\Address")
     * @ORM\JoinColumn(referencedColumnName="address", nullable=false)
     */
    private $address;

    /**
     * @var null|ERC20Token
     * @ORM\OneToOne(targetEntity="App\Entity\Model\ERC20Token", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $erc20Token;

    /**
     * @var null|ERC721Token
     * @ORM\OneToOne(targetEntity="App\Entity\Model\ERC721Token", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $erc721Token;

    /**
     * @var null|string
     * @ORM\Column(type="text", nullable=true)
     */
    private $byteCode;

    public function __construct(Address $address)
    {
        $this->address = $address;
    }

    /**
     * @ORM\PreFlush()
     */
    public function preFlush()
    {
        $this->erc20Token = base64_encode(serialize($this->erc20Token));
    }

    /**
     * @ORM\PostLoad()
     */
    public function preLoad()
    {
        $this->erc20Token = unserialize(base64_decode($this->erc20Token));
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address)
    {
        $this->address = $address;
    }

    public function getErc20Token(): ?ERC20Token
    {
        return $this->erc20Token;
    }

    public function setErc20Token(?ERC20Token $erc20Token)
    {
        $this->erc20Token = $erc20Token;
    }

    public function isErc20Token(): bool
    {
        return $this->hasErc20Token() && !$this->hasErc721Token();
    }

    public function hasErc20Token()
    {
        return !is_null($this->erc20Token);
    }

    public function getErc721Token(): ?ERC721Token
    {
        return $this->erc721Token;
    }

    public function setErc721Token(?ERC721Token $erc721Token)
    {
        $this->erc721Token = $erc721Token;
    }

    public function isErc721Token(): bool
    {
        return !is_null($this->erc721Token);
    }

    public function hasErc721Token(): bool
    {
        return $this->isErc721Token();
    }

    public function getByteCode(): ?string
    {
        return $this->byteCode;
    }

    public function setByteCode(?string $byteCode)
    {
        $this->byteCode = $byteCode;
    }
}
