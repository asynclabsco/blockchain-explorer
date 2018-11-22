<?php

namespace App\Service;

use App\Entity\Contract;
use App\Entity\Transaction;
use App\Enum\GethJsonRPCMethodsEnum;
use App\Event\TransactionReceiptReceivedEvent;
use App\Parser\NodeRequestBuilder;
use App\Repository\ContractRepository;
use App\Repository\TransactionRepository;
use App\Service\TokenDetection\TokenDetectionService;
use Datto\JsonRpc\Client as JsonRpcClient;
use Datto\JsonRpc\Response;

class FetchTransactionReceiptsService
{
    /** @var TransactionRepository */
    private $transactionRepository;

    /** @var NodeRequestBuilder */
    private $nodeRequestBuilder;

    /** @var AddressFinderService */
    private $addressFinderService;

    /** @var EventBus */
    private $eventBus;

    /** @var JsonRpcClient */
    private $jsonRpcClient;

    /** @var ContractRepository */
    private $contractRepository;

    /** @var TokenDetectionService */
    private $tokenDetection;

    public function __construct(
        TransactionRepository $transactionRepository,
        NodeRequestBuilder $nodeRequestBuilder,
        AddressFinderService $addressFinderService,
        EventBus $eventBus,
        ContractRepository $contractRepository,
        TokenDetectionService $tokenDetectionService
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->nodeRequestBuilder = $nodeRequestBuilder;
        $this->addressFinderService = $addressFinderService;
        $this->eventBus = $eventBus;
        $this->contractRepository = $contractRepository;
        $this->tokenDetection = $tokenDetectionService;
        $this->jsonRpcClient = new JsonRpcClient();
    }

    public function fetchTransactionReceipts()
    {
        $transactionsWithoutReceipts = $this->getBatchOfTransactionsWithoutReceipt();

        // Don't do anything if there is no transactions without receipts
        if (empty($transactionsWithoutReceipts)) {
            return;
        }

        $message = $this->createQueryForBatchedTransactionReceipts($transactionsWithoutReceipts);

        $responseArray = $this->nodeRequestBuilder->executeRequest($message);

        $this->parseResponseAndSaveToTransactions($transactionsWithoutReceipts, $responseArray);
    }

    private function createQueryForBatchedTransactionReceipts(array $transactionsWithoutReceipts)
    {
        /** @var Transaction $transactionsWithoutReceipt */
        foreach ($transactionsWithoutReceipts as $transactionsWithoutReceipt) {
            $this->jsonRpcClient->query(
                $transactionsWithoutReceipt->getTxHash(),
                GethJsonRPCMethodsEnum::GET_TRANSACTION_RECEIPT,
                [$transactionsWithoutReceipt->getTxHash()]
            );
        }

        return $this->jsonRpcClient->encode();
    }

    private function getBatchOfTransactionsWithoutReceipt()
    {
        $transactionsWithoutReceipts = $this->transactionRepository->getBatchOfTransactionsWithoutReceipt();

        $array = [];

        /** @var Transaction $transactionsWithoutReceipt */
        foreach ($transactionsWithoutReceipts as $transactionsWithoutReceipt) {
            $array[$transactionsWithoutReceipt->getTxHash()] = $transactionsWithoutReceipt;
        }

        return $array;
    }

    private function parseResponseAndSaveToTransactions(array $transactionsWithoutReceipts, array $responseArray)
    {
        /** @var Response $response */
        foreach ($responseArray as $response) {
            $this->parseSingleTransactionReceipt(
                $transactionsWithoutReceipts[$response->getId()],
                $response->getResult()
            );
        }
    }

    private function parseSingleTransactionReceipt(Transaction $transaction, ?array $responseResult)
    {
        if (is_null($responseResult)) {
            return;
        }

        if (array_key_exists('status', $responseResult)) {
            $transaction->setStatus($responseResult['status']);
        } else {
            // TODO handle when response status is in root key
        }

        $transaction->setGasUsed($responseResult['gasUsed']);
        $transaction->setLogsBloom($responseResult['logsBloom']);

        $this->handleSuccesfulTransaction($transaction);
        $this->handleTransactionCreatedContract($transaction, $responseResult['contractAddress']);

        $this->transactionRepository->save($transaction);

        $this->eventBus->dispatch(new TransactionReceiptReceivedEvent($transaction->getTxHash()));
    }

    private function handleTransactionCreatedContract(Transaction $transaction, ?string $contractAddress)
    {
        if (is_null($contractAddress)) {
            return;
        }

        $address = $this->addressFinderService->findOrCreateAddress($contractAddress);
        $address->markSmartContract();

        $contract = new Contract($address);
        $this->contractRepository->save($contract);

        $transaction->setContractAddress($address);

        $this->tokenDetection->detectTokens($contract);
    }

    private function handleSuccesfulTransaction(Transaction $transaction)
    {
        if (!$transaction->isSuccessful()) {
            return;
        }

        $transaction->getFrom()->subtractBalance($transaction->getValue());
        $transaction->getTo()->addBalance($transaction->getValue());
    }
}
