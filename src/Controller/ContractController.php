<?php

namespace App\Controller;

use App\Repository\ContractRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig_Environment;

class ContractController
{
    /** @var Twig_Environment */
    private $twig;

    /** @var ContractRepository */
    private $contractRepository;

    /** @var PaginatorInterface */
    private $paginator;

    public function __construct(
        Twig_Environment $twig,
        ContractRepository $contractRepository,
        PaginatorInterface $paginator
    ) {
        $this->twig = $twig;
        $this->contractRepository = $contractRepository;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/contracts", name="be.contracts.all_contracts")
     */
    public function allContracts(Request $request)
    {
        $page = $request->query->getInt('page', 1);

        $contractsQb = $this->contractRepository->findAllContractsQb();
        $contracts = $this->paginator->paginate($contractsQb, $page, 20);

        $body = $this->twig->render('Contract/all-contracts.html.twig', [
            'contracts' => $contracts,
        ]);

        return new Response($body);
    }

    /**
     * @Route("/contract/{address}", name="be.contracts.show_contract")
     */
    public function showContract(Request $request, $address)
    {
        $contract = $this->contractRepository->findByAddress($address);

        if (is_null($contract)) {
            throw new NotFoundHttpException();
        }

        $body = $this->twig->render('Contract/show-contract.html.twig', [
            'contract' => $contract,
        ]);

        return new Response($body);
    }
}
