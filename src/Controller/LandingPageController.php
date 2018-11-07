<?php

namespace App\Controller;

use App\Repository\BlockRepository;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig_Environment;

class LandingPageController
{
    /** @var Twig_Environment */
    private $twig;

    /** @var BlockRepository */
    private $blockRepository;

    /** @var TransactionRepository */
    private $transactionRepository;

    public function __construct(
        Twig_Environment $twig,
        BlockRepository $blockRepository,
        TransactionRepository $transactionRepository
    ) {
        $this->twig = $twig;
        $this->blockRepository = $blockRepository;
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * @Route("/", name="be.landing_page")
     */
    public function showLandingPageAction()
    {
        $body = $this->twig->render('landing-page.html.twig', [
            'latestBlocks'       => $this->blockRepository->getLatestBlocks(),
            'latestTransactions' => $this->transactionRepository->getLatestTransactions(),
        ]);

        return new Response($body);
    }
}
