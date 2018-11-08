<?php

namespace App\Repository;

use App\Entity\Block;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class BlockRepository
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var EntityRepository */
    private $repository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $this->em->getRepository(Block::class);
    }

    public function save(Block $block)
    {
        $this->em->persist($block);
        $this->em->flush();
    }

    public function findLatestBlock(): ?Block
    {
        $qb = $this->repository->createQueryBuilder('b');

        $qb->orderBy('b.id', 'DESC');
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findByBlockHash(string $blockHash): ?Block
    {
        return $this->repository->findOneBy(['blockHash' => $blockHash]);
    }

    public function getLatestBlocks(int $limit = 10)
    {
        $qb = $this->repository->createQueryBuilder('b');

        $qb->orderBy('b.id', 'DESC');

        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function findByBlockNumberOrBlockHash($blockHashOrNumber): ?Block
    {
        $qb = $this->repository->createQueryBuilder('b');

        $qb->where('b.blockNumber = :blockHashOrNumber OR b.blockHash = :blockHashOrNumber');

        $qb->setParameter('blockHashOrNumber', $blockHashOrNumber);

        return $qb->getQuery()->getSingleResult();
    }

    public function findAllBlocksQb()
    {
        $qb = $this->repository->createQueryBuilder('b');

        $qb->orderBy('b.id', 'DESC');

        return $qb;
    }
}
