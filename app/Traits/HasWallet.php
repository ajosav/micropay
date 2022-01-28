<?php

namespace App\Traits;

use App\Models\Transfer;
use App\Contracts\Wallet;
use App\Models\Transaction;
use App\Facades\MicropayUtil;
use App\Models\Wallet as WalletModel;
use App\Contracts\CastServiceInterface;
use App\Contracts\AtomicServiceInterface;
use App\Contracts\WalletServiceInterface;
use App\Contracts\RegulatorServiceInterface;
use App\Exceptions\MicropayException;
use App\Services\Wallet\CommonLegacyService;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasWallet
{
    /**
     * The variable is used for the cache, so as not to request wallets many times.
     * WalletProxy keeps the money wallets in the memory to avoid errors when you
     * purchase/transfer, etc.
     */
    private array $_wallets = [];

    private bool $_loadedWallets = false;
    /**
     * The input means in the system.
     *
     * @param int|string $amount
     *
     * @throws AmountInvalid
     * @throws LockProviderNotFoundException
     * @throws RecordsNotFoundException
     * @throws TransactionFailedException
     * @throws ExceptionInterface
     */
    public function deposit($amount, ?array $meta = null, bool $confirmed = false): Transaction
    {
        
        return app(AtomicServiceInterface::class)->block(
            $this,
            fn () => app(CommonLegacyService::class)
                ->makeTransaction($this, Transaction::TYPE_DEPOSIT, $amount, $meta, $confirmed)
        );
    }


    public function getBalanceAttribute()
    {
        /** @var Wallet $this */
        return app(RegulatorServiceInterface::class)->amount(
            app(CastServiceInterface::class)->getWallet($this)
        );
    }

    public function getBalanceIntAttribute(): int
    {
        return (int) $this->getBalanceAttribute();
    }

    /**
     * We receive transactions of the selected wallet.
     */
    public function walletTransactions(): HasMany
    {
        return app(CastServiceInterface::class)
            ->getWallet($this)
            ->hasMany(config('wallet.transaction.model', Transaction::class), 'wallet_id')
        ;
    }

    /**
     * all user actions on wallets will be in this method.
     */
    public function transactions(): MorphMany
    {
        return app(CastServiceInterface::class)
            ->getHolder($this)
            ->morphMany(config('wallet.transaction.model', Transaction::class), 'payable')
        ;
    }

    /**
     * This method ignores errors that occur when transferring funds.
     *
     * @param int|string $amount
     */
    public function safeTransfer(Wallet $wallet, $amount, ?array $meta = null): ?Transfer
    {
        return $this->transfer($wallet, $amount, $meta);
        
    }

    /**
     * A method that transfers funds from host to host.
     *
     * @param int|string $amount
     *
     * @throws AmountInvalid
     * @throws BalanceIsEmpty
     * @throws InsufficientFunds
     * @throws LockProviderNotFoundException
     * @throws RecordsNotFoundException
     * @throws TransactionFailedException
     * @throws ExceptionInterface
     */
    public function transfer(Wallet $wallet, $amount, ?array $meta = null): Transfer
    {
        /** @var Wallet $this */
    
        MicropayUtil::checkPotential($this, $amount);

        return $this->forceTransfer($wallet, $amount, $meta);
    }

    /**
     * Withdrawals from the system.
     *
     * @param int|string $amount
     *
     * @throws AmountInvalid
     * @throws BalanceIsEmpty
     * @throws InsufficientFunds
     * @throws LockProviderNotFoundException
     * @throws RecordsNotFoundException
     * @throws TransactionFailedException
     * @throws ExceptionInterface
     */
    public function withdraw($amount, ?array $meta = null, bool $confirmed = true): Transaction
    {
        /** @var Wallet $this */
        MicropayUtil::checkPotential($this, $amount);

        return $this->forceWithdraw($amount, $meta, $confirmed);
    }

    /**
     * Checks if you can withdraw funds.
     *
     * @param float|int|string $amount
     */
    public function canWithdraw($amount, bool $allowZero = false): bool
    {
        $mathService = app(MathServiceInterface::class);
        $wallet = app(CastServiceInterface::class)->getWallet($this);
        $balance = $mathService->add($this->getBalanceAttribute(), $wallet->getCreditAttribute());

        return MicropayUtil::canWithdraw($balance, $amount, $allowZero);
    }

    /**
     * Forced to withdraw funds from system.
     *
     * @param int|string $amount
     *
     * @throws AmountInvalid
     * @throws LockProviderNotFoundException
     * @throws RecordsNotFoundException
     * @throws TransactionFailedException
     * @throws ExceptionInterface
     */
    public function forceWithdraw($amount, ?array $meta = null, bool $confirmed = true): Transaction
    {
        return app(AtomicServiceInterface::class)->block(
            $this,
            fn () => app(CommonLegacyService::class)
                ->makeTransaction($this, Transaction::TYPE_WITHDRAW, $amount, $meta, $confirmed)
        );
    }

    /**
     * the forced transfer is needed when the user does not have the money and we drive it.
     * Sometimes you do. Depends on business logic.
     *
     * @param int|string $amount
     *
     * @throws AmountInvalid
     * @throws LockProviderNotFoundException
     * @throws RecordsNotFoundException
     * @throws TransactionFailedException
     * @throws ExceptionInterface
     */
    public function forceTransfer(Wallet $wallet, $amount, ?array $meta = null): Transfer
    {
        return app(AtomicServiceInterface::class)->block(
            $this,
            fn () => app(CommonLegacyService::class)
                ->forceTransfer($this, $wallet, $amount, $meta)
        );
    }

    /**
     * the transfer table is used to confirm the payment
     * this method receives all transfers.
     */
    public function transfers(): MorphMany
    {
        /** @var Wallet $this */
        return app(CastServiceInterface::class)
            ->getWallet($this, false)
            ->morphMany(config('wallet.transfer.model', Transfer::class), 'from')
        ;
    }

    /**
     * Get wallet by slug.
     *
     *  $user->wallet->balance // 200
     *  or short recording $user->balance; // 200
     *
     *  $defaultSlug = config('wallet.wallet.default.slug');
     *  $user->getWallet($defaultSlug)->balance; // 200
     *
     *  $user->getWallet('usd')->balance; // 50
     *  $user->getWallet('rub')->balance; // 100
     */
    public function getWallet(string $name = 'default'): ?WalletModel
    {
        return $this->getWalletOrFail($name);
    }

    public function baseWallet(): ?WalletModel
    {
        try {
            if(!$this->wallets()->count())
            {
                return $this->createWallet();
            }

            return app(WalletServiceInterface::class)->findByHolder($this);
        } catch (MicropayException $e) {
            return $this->createWallet();
        }
        
    }

    /**
     * Get wallet by slug.
     *
     *  $user->wallet->balance // 200
     *  or short recording $user->balance; // 200
     *
     *  $defaultSlug = config('wallet.wallet.default.slug');
     *  $user->getWallet($defaultSlug)->balance; // 200
     *
     *  $user->getWallet('usd')->balance; // 50
     *  $user->getWallet('rub')->balance; // 100
     *
     * @throws ModelNotFoundException
     */
    public function getWalletOrFail(string $name): WalletModel
    {
        if (!$this->_loadedWallets && $this->relationLoaded('wallets')) {
            $this->_loadedWallets = true;
            $wallets = $this->getRelation('wallets');
            foreach ($wallets as $wallet) {
                $this->_wallets[$wallet->name] = $wallet;
            }
        }
        
        if (!array_key_exists($name, $this->_wallets)) {
            $this->_wallets[$name] = app(WalletServiceInterface::class)->getByName($this, $name);
        }

        return $this->_wallets[$name];
    }

    /**
     * method of obtaining all wallets.
     */
    public function wallets(): MorphOne
    {
        return $this->morphOne(config('wallet.wallet.model', WalletModel::class), 'holder');
    }

    public function createWallet(array $data = []): WalletModel
    {
        $wallet = app(WalletServiceInterface::class)->create($this, $data);
        $this->_wallets[$wallet->wallet_name] = $wallet;

        return $wallet;
    }

    /**
     * The method checks the existence of the wallet.
     */
    public function hasWallet(string $name = 'default'): bool
    {
        return (bool) $this->getWallet($name);
    }
}