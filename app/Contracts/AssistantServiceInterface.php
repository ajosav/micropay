<?php

namespace App\Contracts;

use App\Services\Dto\TransferDtoInterface;
use App\Services\Dto\TransactionDtoInterface;

interface AssistantServiceInterface
{
    public function getReferences(array $objects): array;

    /**
     * @param non-empty-array<TransactionDtoInterface> $transactions
     *
     * @return array<int, string>
     */
    public function getSums(array $transactions): array;
}
