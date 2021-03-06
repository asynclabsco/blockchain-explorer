<?php

namespace App\Controller;

use App\Repository\AddressRepository;
use App\Repository\TransactionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
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

    /** @var RouterInterface */
    private $router;

    public function __construct(
        Twig_Environment $twig,
        AddressRepository $addressRepository,
        TransactionRepository $transactionsRepository,
        PaginatorInterface $paginator,
        RouterInterface $router
    ) {
        $this->twig = $twig;
        $this->addressRepository = $addressRepository;
        $this->transactionsRepository = $transactionsRepository;
        $this->paginator = $paginator;
        $this->router = $router;
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

        if ($address->isSmartContractAddress()) {
            $contractUrl = $this->router->generate('be.contracts.show_contract', [
                'address' => $address->getAddress(),
            ]);

            return new RedirectResponse($contractUrl);
        }

        $transactionsQb = $this->transactionsRepository->findTransactionsByAddress($address);
        $transactions = $this->paginator->paginate($transactionsQb, $page, 20);

        $body = $this->twig->render('Address/show-address.html.twig', [
            'address'      => $address,
            'transactions' => $transactions,
        ]);

        return new Response($body);
    }

    /**
     * @Route("/addresses", name="be.address.show_all_addresses")
     */
    public function showAllAddresses(Request $request)
    {
        $page = $request->query->getInt('page', 1);

        $addressesQb = $this->addressRepository->findAllAddresesQb();
        $addresses = $this->paginator->paginate($addressesQb, $page, 10);

        $body = $this->twig->render('Address/all-addresses.html.twig', [
            'addresses' => $addresses,
        ]);

        return new Response($body);
    }
}
