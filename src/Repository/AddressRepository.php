<?php

namespace App\Repository;

use App\Entity\Address;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class AddressRepository
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var EntityRepository */
    private $repository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $this->em->getRepository(Address::class);
    }

    public function findOrCreateAddress(string $walletAddress): Address
    {
        $address = $this->repository->find($walletAddress);

        if (is_null($address)) {
            $address = new Address($walletAddress);
            $this->save($address);
        }

        return $address;
    }

    private function save(Address $address)
    {
        $this->em->persist($address);
        $this->em->flush();
    }

    public function find($address): ?Address
    {
        return $this->repository->find($address);
    }
}
