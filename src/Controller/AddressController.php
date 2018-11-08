<?php

namespace App\Controller;

use App\Repository\AddressRepository;
use App\Repository\TransactionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig_Environment;

class AddressController
{
    /** @var Twig_Environment */
    private $twig;

    /** @var AddressRepository */
    private $addressRepository;

    /** @var TransactionRepository */
    private $transactionsRepository;

    /** @var PaginatorInterface */
    private $paginator;

    public function __construct(
        Twig_Environment $twig,
        AddressRepository $addressRepository,
        TransactionRepository $transactionsRepository,
        PaginatorInterface $paginator
    ) {
        $this->twig = $twig;
        $this->addressRepository = $addressRepository;
        $this->transactionsRepository = $transactionsRepository;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/address/{address}", name="be.address.show_address")
     */
    public function showAddress(Request $request, $address)
    {
        $page = $request->query->getInt('page', 1);

        $address = $this->addressRepository->find($address);

        if (is_null($address)) {
            throw new NotFoundHttpException();
        }

        $transactionsQb = $this->transactionsRepository->findTransactionsByAddress($address);
        $transactions = $this->paginator->paginate($transactionsQb, $page, 10);

        $body = $this->twig->render('Address/show-address.html.twig', [
            'address'      => $address,
            'transactions' => $transactions,
        ]);

        return new Response($body);
    }
}
