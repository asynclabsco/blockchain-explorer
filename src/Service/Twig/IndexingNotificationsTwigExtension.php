<?php

namespace App\Service\Twig;

use App\Repository\BlockchainRepository;
use Twig_Environment;
use Twig_Extension;
use Twig_Function;

class IndexingNotificationsTwigExtension extends Twig_Extension
{
    /** @var Twig_Environment */
    private $twig;

    /** @var BlockchainRepository */
    private $blockchainRepository;

    public function __construct(Twig_Environment $twig, BlockchainRepository $blockchainRepository)
    {
        $this->twig = $twig;
        $this->blockchainRepository = $blockchainRepository;
    }

    public function getFunctions()
    {
        return [
            new Twig_Function(
                'indexingNotification',
                [$this, 'getIndexingNotification'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function getIndexingNotification()
    {
        $blockchain = $this->blockchainRepository->getBlockchain();

        return $this->twig->render('General/indexing-notification.html.twig', [
            'blockchain' => $blockchain,
        ]);
    }
}
