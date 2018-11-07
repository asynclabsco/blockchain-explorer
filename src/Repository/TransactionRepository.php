<?php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class TransactionRepository
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var EntityRepository */
    private $repository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $this->em->getRepository(Transaction::class);
    }

    public function save(Transaction $transaction)
    {
        $this->em->persist($transaction);
        $this->em->flush();
    }

    public function getLatestTransactions(int $limit = 10)
    {
        $qb = $this->repository->createQueryBuilder('t');

        $qb->join('t.block', 'b');

        $qb->orderBy('b.id', 'DESC');
        $qb->orderBy('t.index', 'ASC');

        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }
}
