<?php

namespace App\Contracts;

use App\Models\Transfer;
use App\Contracts\Wallet;
use App\Models\Transaction;
use App\Exceptions\ExceptionInterface;
use App\Exceptions\TransactionFailedException;
use App\Exceptions\LockProviderNotFoundException;
use Illuminate\Database\RecordsNotFoundException;

interface WalletFloat
{
    /**
     * @param float|string $amount
     *
     * @throws AmountInvalid
     * @throws LockProviderNotFoundException
     * @throws RecordsNotFoundException
     * @throws TransactionFailedException
     * @throws ExceptionInterface
     */
    public function depositFloat($amount, ?array $meta = null, bool $confirmed = true): Transaction;

    /**
     * @param float|string $amount
     *
     * 
     */
    public function withdrawFloat($amount, ?array $meta = null, bool $confirmed = true): Transaction;

    /**
     * @param float|string $amount
     *
     */
    public function forceWithdrawFloat($amount, ?array $meta = null, bool $confirmed = true): Transaction;

    /**
     * @param float|string $amount
     *
     * 
     */
    public function transferFloat(Wallet $wallet, $amount, ?array $meta = null): Transfer;

    /**
     * @param float|string $amount
     */
    public function safeTransferFloat(Wallet $wallet, $amount, ?array $meta = null): ?Transfer;

    /**
     * @param float|string $amount
     *
     * @throws AmountInvalid
     * @throws LockProviderNotFoundException
     * @throws RecordsNotFoundException
     * @throws TransactionFailedException
     * @throws ExceptionInterface
     */
    public function forceTransferFloat(Wallet $wallet, $amount, ?array $meta = null): Transfer;

    /**
     * @param float|string $amount
     */
    public function canWithdrawFloat($amount): bool;

    /**
     * @return float|int|string
     */
    public function getBalanceFloatAttribute();
}
