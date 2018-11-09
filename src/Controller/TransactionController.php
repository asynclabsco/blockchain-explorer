<?php

namespace App\Controller;

use App\Repository\TransactionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig_Environment;

class TransactionController
{
    /** @var Twig_Environment */
    private $twig;

    /** @var TransactionRepository */
    private $transactionRepository;

    /** @var PaginatorInterface */
    private $paginator;

    public function __construct(
        Twig_Environment $twig,
        TransactionRepository $transactionRepository,
        PaginatorInterface $paginator
    ) {
        $this->twig = $twig;
        $this->transactionRepository = $transactionRepository;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/tx/{id}", name="be.transaction.show_transaction")
     */
    public function showTransaction(Request $request, $id)
    {
        $transaction = $this->transactionRepository->find($id);

        if (is_null($transaction)) {
            throw new NotFoundHttpException();
        }

        $body = $this->twig->render('Transaction/show-transaction.html.twig', [
            'transaction' => $transaction,
        ]);

        return new Response($body);
    }

    /**
     * @Route("/transactions", name="be.transaction.show_all_transactions")
     */
    public function showAllTransactions(Request $request)
    {
        $page = $request->query->getInt('page', 1);

        $transactionQb = $this->transactionRepository->findAllTransactionsQb();

        $transactions = $this->paginator->paginate($transactionQb, $page, 20);

        $body = $this->twig->render('Transaction/all-transactions.html.twig', [
            'transactions' => $transactions,
        ]);

        return new Response($body);
    }
}
