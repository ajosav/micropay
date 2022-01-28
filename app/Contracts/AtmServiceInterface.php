<?php

namespace App\Contracts;

interface AtmServiceInterface
{
    public function makeTransactions(array $objects): array;

    public function makeTransfers(array $objects): array;
}
