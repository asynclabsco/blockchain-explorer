<?php

namespace App\Controller;

use App\Repository\ContractRepository;
use App\Repository\TransactionRepository;
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

    /** @var TransactionRepository */
    private $transactionRepository;

    public function __construct(
        Twig_Environment $twig,
        ContractRepository $contractRepository,
        PaginatorInterface $paginator,
        TransactionRepository $transactionRepository
    ) {
        $this->twig = $twig;
        $this->contractRepository = $contractRepository;
        $this->paginator = $paginator;
        $this->transactionRepository = $transactionRepository;
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
        $page = $request->query->getInt('page', 1);
        $contract = $this->contractRepository->findByAddress($address);

        if (is_null($contract)) {
            throw new NotFoundHttpException();
        }

        $transactionsQb = $this->transactionRepository->findTransactionsByContractQb($contract);
        $transactions = $this->paginator->paginate($transactionsQb, $page, 20);

        $body = $this->twig->render('Contract/show-contract.html.twig', [
            'contract'     => $contract,
            'transactions' => $transactions,
        ]);

        return new Response($body);
    }
}
