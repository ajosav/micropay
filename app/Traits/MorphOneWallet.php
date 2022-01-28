<?php

namespace App\Traits;

use App\Models\Wallet;
use App\Contracts\CastServiceInterface;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Trait MorphOneWallet.
 *
 * @property Wallet $wallet
 */
trait MorphOneWallet
{
    /**
     * Get default Wallet
     * this method is used for Eager Loading.
     */
    public function wallet(): MorphOne
    {
        return app(CastServiceInterface::class)
            ->getHolder($this)
            ->morphOne(Wallet::class, 'holder');
    }
}