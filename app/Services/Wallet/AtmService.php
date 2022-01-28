<?php

namespace App\Services\Wallet;

use App\Contracts\AtmServiceInterface;
use App\Contracts\AssistantServiceInterface;
use App\Contracts\Repository\TransferRepositoryInterface;
use App\Contracts\Repository\TransactionRepositoryInterface;

/** @psalm-internal */
final class AtmService implements AtmServiceInterface
{
    private TransactionRepositoryInterface $transactionRepository;
    private TransferRepositoryInterface $transferRepository;
    private AssistantServiceInterface $assistantService;

    public function __construct(
        TransactionRepositoryInterface $transactionRepository,
        TransferRepositoryInterface $transferRepository,
        AssistantServiceInterface $assistantService
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->transferRepository = $transferRepository;
        $this->assistantService = $assistantService;
    }

    public function makeTransactions(array $objects): array
    {
        if (count($objects) === 1) {
            $items = [$this->transactionRepository->insertOne(reset($objects))];
        } else {
            $this->transactionRepository->insert($objects);
            $refernces = $this->assistantService->getReferences($objects);
            $items = $this->transactionRepository->findBy($refernces);
        }

        assert(count($items) > 0);

        $results = [];
        foreach ($items as $item) {
            $results[$item->reference] = $item;
        }

        return $results;
    }

    public function makeTransfers(array $objects): array
    {
        if (count($objects) === 1) {
            $items = [$this->transferRepository->insertOne(reset($objects))];
        } else {
            $this->transferRepository->insert($objects);
            $refernces = $this->assistantService->getReferences($objects);
            $items = $this->transferRepository->findBy($refernces);
        }

        assert(count($items) > 0);

        $results = [];
        foreach ($items as $item) {
            $results[$item->reference] = $item;
        }

        return $results;
    }
}
