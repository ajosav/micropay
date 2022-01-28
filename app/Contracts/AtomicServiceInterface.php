<?php

namespace App\Contracts;

use App\Contracts\Wallet;
use App\Exceptions\ExceptionInterface;
use App\Exceptions\TransactionFailedException;
use App\Exceptions\LockProviderNotFoundException;
use Illuminate\Database\RecordsNotFoundException;

interface AtomicServiceInterface
{
    /**
     * @throws LockProviderNotFoundException
     * @throws RecordsNotFoundException
     * @throws TransactionFailedException
     * @throws ExceptionInterface
     *
     * @return mixed
     */
    public function block(Wallet $object, callable $callback);
}
