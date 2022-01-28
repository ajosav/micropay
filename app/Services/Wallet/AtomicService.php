<?php

namespace App\Services\Wallet;

use App\Contracts\Wallet;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ExceptionInterface;
use App\Contracts\CastServiceInterface;
use App\Contracts\AtomicServiceInterface;
use App\Exceptions\TransactionFailedException;
use App\Exceptions\LockProviderNotFoundException;
use Illuminate\Database\RecordsNotFoundException;
use App\Contracts\Repository\LockServiceInterface;

final class AtomicService implements AtomicServiceInterface
{
    private const PREFIX = 'wallet_atomic::';
    private LockServiceInterface $lockService;
    private CastServiceInterface $castService;

    public function __construct(
        LockServiceInterface $lockService,
        CastServiceInterface $castService
    ) {
        $this->lockService = $lockService;
        $this->castService = $castService;
    }

    /**
     * @throws LockProviderNotFoundException
     * @throws RecordsNotFoundException
     * @throws TransactionFailedException
     * @throws ExceptionInterface
     *
     * @return mixed
     */
    public function block(Wallet $object, callable $callback)
    {
        return $this->lockService->block(
            $this->key($object),
            fn () => DB::transaction($callback)
        );
    }

    private function key(Wallet $object): string
    {
        $wallet = $this->castService->getWallet($object);

        return self::PREFIX.'::'.get_class($wallet).'::'.$wallet->reference;
    }
}
