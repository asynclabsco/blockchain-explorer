<?php

namespace App\Repository;

use App\Entity\Blockchain;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use DomainException;

class BlockchainRepository
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var EntityRepository */
    private $repository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $this->em->getRepository(Blockchain::class);
    }

    public function updateBlockchainBlockHeight($newBlockHeight)
    {
        $blockchain = $this->getBlockchain();

        if (is_null($blockchain)) {
            throw new DomainException('Blockchain is not initialized');
        }

        $blockchain->setBlockchainBlockHeight($newBlockHeight);

        $this->save($blockchain);
    }

    public function save(Blockchain $blockchain)
    {
        $this->em->persist($blockchain);
        $this->em->flush();
    }

    public function getBlockchain(): Blockchain
    {
        $blockchain = $this->repository->findOneBy([]);

        if (is_null($blockchain)) {
            $blockchain = new Blockchain();
        }

        $this->save($blockchain);

        return $blockchain;
    }
}
