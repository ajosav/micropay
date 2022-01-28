<?php

namespace App\Services\Dto;

use App\Contracts\Wallet;

interface TransferLazyDtoInterface
{
    public function getFromWallet(): Wallet;

    public function getToWallet(): Wallet;

    public function getWithdrawDto(): TransactionDtoInterface;

    public function getDepositDto(): TransactionDtoInterface;

    public function getStatus(): string;
}
