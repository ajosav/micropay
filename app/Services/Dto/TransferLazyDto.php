<?php

namespace App\Services\Dto;

use App\Contracts\Wallet;
use App\Services\Dto\TransferLazyDtoInterface;

/** @psalm-immutable */
final class TransferLazyDto implements TransferLazyDtoInterface
{
    private Wallet $fromWallet;
    private Wallet $toWallet;
    private TransactionDtoInterface $depositDto;
    private TransactionDtoInterface $withdrawDto;

    private string $status;

    public function __construct(
        Wallet $fromWallet,
        Wallet $toWallet,
        TransactionDtoInterface $withdrawDto,
        TransactionDtoInterface $depositDto,
        string $status
    ) {
        $this->fromWallet = $fromWallet;
        $this->toWallet = $toWallet;

        $this->withdrawDto = $withdrawDto;
        $this->depositDto = $depositDto;

        $this->status = $status;
    }

    public function getFromWallet(): Wallet
    {
        return $this->fromWallet;
    }

    public function getToWallet(): Wallet
    {
        return $this->toWallet;
    }

    public function getWithdrawDto(): TransactionDtoInterface
    {
        return $this->withdrawDto;
    }

    public function getDepositDto(): TransactionDtoInterface
    {
        return $this->depositDto;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
