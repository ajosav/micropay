<?php

namespace App\Contracts\Repository;

use App\Models\Transaction;
use App\Services\Dto\TransactionDtoInterface;

interface TransactionRepositoryInterface
{
    /**
     * @param non-empty-array<int|string, TransactionDtoInterface> $objects
     */
    public function insert(array $objects): void;

    public function insertOne(TransactionDtoInterface $dto): Transaction;

    /** @return Transaction[] */
    public function findBy($query): array;
}
