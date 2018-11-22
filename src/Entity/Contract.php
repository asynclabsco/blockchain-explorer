<?php

namespace App\Entity;

use App\Entity\Model\ERC20TokenValidation;
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
     * @var ERC20TokenValidation
     * @ORM\OneToOne(targetEntity="App\Entity\Model\ERC20TokenValidation", cascade={"persist"})
     */
    private $erc20TokenValidation;

    public function __construct(Address $address)
    {
        $this->address = $address;
        $this->erc20TokenValidation = new ERC20TokenValidation();
    }

    /**
     * @ORM\PreFlush()
     */
    public function preFlush()
    {
        $this->erc20TokenValidation = base64_encode(serialize($this->erc20TokenValidation));
    }

    /**
     * @ORM\PostLoad()
     */
    public function preLoad()
    {
        $this->erc20TokenValidation = unserialize(base64_decode($this->erc20TokenValidation));
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

    public function getErc20TokenValidation(): ERC20TokenValidation
    {
        return $this->erc20TokenValidation;
    }

    public function setErc20TokenValidation(ERC20TokenValidation $erc20TokenValidation)
    {
        $this->erc20TokenValidation = $erc20TokenValidation;
    }

    public function isErc20Token(): bool
    {
        return $this->erc20TokenValidation->isERC20Token();
    }
}
