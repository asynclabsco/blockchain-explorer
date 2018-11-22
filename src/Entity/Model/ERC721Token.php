<?php

namespace App\Entity\Model;

use App\Entity\Contract;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class ERC721Token
{
    /**
     * @var integer
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", nullable=false, unique=true)
     */
    private $id;

    /**
     * @var Contract
     * @ORM\OneToOne(targetEntity="App\Entity\Contract")
     * @ORM\JoinColumn()
     */
    private $contract;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false, options={})
     */
    private $transferEventExists = false;

    /**
     * @var null|string
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;

    /**
     * @var null|string
     * @ORM\Column(type="string", nullable=true)
     */
    private $symbol;

    /**
     * @var string
     * @ORM\Column(type="decimal", precision=100, scale=0, nullable=true)
     */
    private $totalSupply = 0;

    /**
     * @var array
     * @ORM\Column(type="array", nullable=true)
     */
    private $foundInterfaceMethods;

    /**
     * @var array
     * @ORM\Column(type="array", nullable=true)
     */
    private $notFoundInterfaceMethods;

    public function __construct(Contract $contract)
    {
        $this->contract = $contract;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getContract(): Contract
    {
        return $this->contract;
    }

    public function setContract(Contract $contract)
    {
        $this->contract = $contract;
    }

    public function isTransferEventExists(): bool
    {
        return $this->transferEventExists;
    }

    public function setTransferEventExists(bool $transferEventExists)
    {
        $this->transferEventExists = $transferEventExists;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name)
    {
        $this->name = $name;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(?string $symbol)
    {
        $this->symbol = $symbol;
    }

    public function getTotalSupply(): string
    {
        return $this->totalSupply;
    }

    public function setTotalSupply(string $totalSupply)
    {
        $this->totalSupply = $totalSupply;
    }

    public function isErc721Token(): bool
    {
        // Or has approval events
        return $this->transferEventExists || count($this->foundInterfaceMethods) > count($this->notFoundInterfaceMethods);
    }

    public function getFoundInterfaceMethods(): array
    {
        return $this->foundInterfaceMethods;
    }

    public function setFoundInterfaceMethods(array $foundInterfaceMethods)
    {
        $this->foundInterfaceMethods = $foundInterfaceMethods;
    }

    public function getNotFoundInterfaceMethods(): array
    {
        return $this->notFoundInterfaceMethods;
    }

    public function setNotFoundInterfaceMethods(array $notFoundInterfaceMethods)
    {
        $this->notFoundInterfaceMethods = $notFoundInterfaceMethods;
    }
}
