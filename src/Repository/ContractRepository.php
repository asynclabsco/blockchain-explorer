<?php

namespace App\Repository;

use App\Entity\Contract;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class ContractRepository
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var EntityRepository */
    private $repository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $this->em->getRepository(Contract::class);
    }

    public function save(Contract $contract)
    {
        $this->em->persist($contract);
        $this->em->flush();
    }

    public function findAllContractsQb(): QueryBuilder
    {
        $qb = $this->repository->createQueryBuilder('c');

        return $qb;
    }

    public function findByAddress($address)
    {
        return $this->repository->findOneBy(['address' => $address]);
    }
}
