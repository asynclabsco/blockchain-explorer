<?php

namespace App\Controller;

use App\Repository\BlockRepository;
use App\Repository\TransactionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig_Environment;

class BlockController
{
    /** @var Twig_Environment */
    private $twig;

    /** @var BlockRepository */
    private $blockRepository;

    /** @var TransactionRepository */
    private $transactionRepository;

    /** @var PaginatorInterface */
    private $paginator;

    public function __construct(
        Twig_Environment $twig,
        BlockRepository $blockRepository,
        TransactionRepository $transactionRepository,
        PaginatorInterface $paginator
    ) {
        $this->twig = $twig;
        $this->blockRepository = $blockRepository;
        $this->transactionRepository = $transactionRepository;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/block/{blockHashOrNumber}", name="be.block.show_block")
     */
    public function showBlockAction(Request $request, $blockHashOrNumber)
    {
        $page = $request->query->getInt('page', 1);

        $block = $this->blockRepository->findByBlockNumberOrBlockHash($blockHashOrNumber);

        if (is_null($block)) {
            throw new NotFoundHttpException();
        }

        $transactionsQb = $this->transactionRepository->findTransactionsByBlockQb($block);
        $transactions = $this->paginator->paginate(
            $transactionsQb,
            $page,
            10
        );

        $body = $this->twig->render('Block/show-block.html.twig', [
            'block'        => $block,
            'transactions' => $transactions,
        ]);

        return new Response($body);
    }

    /**
     * @Route("/blocks", name="be.block.get_all_blocks")
     */
    public function getAllBlocks(Request $request)
    {
        $page = $request->query->getInt('page', 1);

        $blocksQb = $this->blockRepository->findAllBlocksQb();
        $blocks = $this->paginator->paginate($blocksQb, $page, 10);

        $body = $this->twig->render('Block/all-blocks.html.twig', [
            'blocks' => $blocks,
        ]);

        return new Response($body);
    }
}
