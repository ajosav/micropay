<?php
namespace App\Contracts;

use App\Contracts\Wallet;
use App\Services\Dto\TransactionDtoInterface;
use App\Services\Dto\TransferLazyDtoInterface;

interface PrepareServiceInterface
{
    public function deposit(Wallet $wallet, string $amount, ?array $meta, bool $confirmed = true): TransactionDtoInterface;

    public function withdraw(Wallet $wallet, string $amount, ?array $meta, bool $confirmed = true): TransactionDtoInterface;

    public function transferLazy(Wallet $from, Wallet $to, string $status, $amount, ?array $meta = null): TransferLazyDtoInterface;
}
