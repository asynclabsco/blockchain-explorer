<?php

namespace App\Repository;

use App\Entity\Address;
use App\Entity\Block;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

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
        $qb->addOrderBy('t.index', 'DESC');

        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function findTransactionsByBlockQb(Block $block): QueryBuilder
    {
        $qb = $this->repository->createQueryBuilder('t');

        $qb->join('t.block', 'b');

        $qb->where('b = :block');
        $qb->setParameter('block', $block);

        $qb->orderBy('b.id', 'DESC');
        $qb->addOrderBy('t.index', 'DESC');

        return $qb;
    }

    public function findTransactionsByAddress(Address $address): QueryBuilder
    {
        $qb = $this->repository->createQueryBuilder('t');

        $qb->join('t.block', 'b');

        $qb->andWhere('t.from = :address OR t.to = :address');
        $qb->setParameter('address', $address);

        $qb->orderBy('b.id', 'DESC');
        $qb->addOrderBy('t.index', 'DESC');

        return $qb;
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function findAllTransactionsQb()
    {
        $qb = $this->repository->createQueryBuilder('t');

        $qb->join('t.block', 'b');

        $qb->orderBy('b.id', 'DESC');
        $qb->addOrderBy('t.index', 'DESC');

        return $qb;
    }

    public function getBatchOfTransactionsWithoutReceipt()
    {
        $qb = $this->repository->createQueryBuilder('t');

        $qb->where('t.status IS NULL');

        $qb->setMaxResults(50);

        return $qb->getQuery()->getResult();
    }

    public function findSuccessfullTransactionsForBlock(Block $block)
    {
        $qb = $this->findTransactionsByBlockQb($block);

        $qb->andWhere('t.status = true');

        return $qb->getQuery()->getResult();
    }
}
