<?php

namespace App\Contracts;

use App\Models\Wallet;
use App\Exceptions\RecordNotFoundException;
use App\Exceptions\LockProviderNotFoundException;

interface BookkeeperServiceInterface
{
    public function missing(Wallet $wallet): bool;

    public function amount(Wallet $wallet): string;

    /**
     * @param float|int|string $value
     *
     * @throws LockProviderNotFoundException
     * @throws RecordNotFoundException
     */
    public function sync(Wallet $wallet, $value): bool;

    /**
     * @param float|int|string $value
     *
     * @throws LockProviderNotFoundException
     * @throws RecordNotFoundException
     */
    public function increase(Wallet $wallet, $value): string;
}
