<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Support\Str;
use App\Contracts\WalletFloat;
use App\Traits\HasWalletFloat;
use App\Contracts\MathServiceInterface;
use Illuminate\Database\Eloquent\Model;
use App\Contracts\AtomicServiceInterface;
use App\Contracts\RegulatorServiceInterface;
use App\Contracts\Wallet as ContractsWallet;
use App\Traits\CanConfirm;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wallet extends Model implements WalletFloat, ContractsWallet
{
    use HasFactory, Uuids, HasWalletFloat, CanConfirm;

    protected $guarded = ['id'];

    protected $primaryKey = 'wallet_unique_id';

    public function getRouteKeyName()
    {
        return 'wallet_unique_id';
    }

    /**
     * @var array
     */
    protected $casts = [
        'meta' => 'json',
    ];

    protected $attributes = [

    ];

    public function getTable(): string
    {
        if (!$this->table) {
            $this->table = config('wallet.wallet.table', 'wallets');
        }

        return parent::getTable();
    }

    /**
     * Under ideal conditions, you will never need a method.
     * Needed to deal with out-of-sync.
     *
     * @throws LockProviderNotFoundException
     * @throws RecordsNotFoundException
     * @throws TransactionFailedException
     * @throws ExceptionInterface
     */
    public function refreshBalance(): bool
    {
        return app(AtomicServiceInterface::class)->block($this, function () {
            $whatIs = $this->getBalanceAttribute();
            $balance = $this->getAvailableBalanceAttribute();
            if (app(MathServiceInterface::class)->compare($whatIs, $balance) === 0) {
                return true;
            }

            return app(RegulatorServiceInterface::class)->sync($this, $balance);
        });
    }

    /** @codeCoverageIgnore */
    public function getOriginalBalanceAttribute(): string
    {
        if (method_exists($this, 'getRawOriginal')) {
            return (string) $this->getRawOriginal('balance', 0);
        }

        return (string) $this->getOriginal('balance', 0);
    }

    /**
     * @return float|int
     */
    public function getAvailableBalanceAttribute()
    {
        return $this->walletTransactions()
            ->where('confirmed', true)
            ->sum('amount')
        ;
    }

    /**
     * @deprecated
     * @see getAvailableBalanceAttribute
     * @codeCoverageIgnore
     *
     * @return float|int
     */
    public function getAvailableBalance()
    {
        return $this->getAvailableBalanceAttribute();
    }

    public function holder(): MorphTo
    {
        return $this->morphTo();
    }

    public function getCreditAttribute(): string
    {
        return (string) ($this->meta['credit'] ?? '0');
    }

    public function getCurrencyAttribute(): string
    {
        return $this->meta['currency'] ?? Str::upper($this->name);
    }

}
