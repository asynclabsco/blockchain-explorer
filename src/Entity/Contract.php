<?php

namespace App\Entity;

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

    //     TODO Rest of attributes

    public function __construct(Address $address)
    {
        $this->address = $address;
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
}
