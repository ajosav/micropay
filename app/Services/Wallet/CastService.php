<?php

namespace App\Services\Wallet;

use App\Contracts\Wallet;
use App\Models\Wallet as WalletModel;
use App\Contracts\CastServiceInterface;
use Illuminate\Database\Eloquent\Model;

/** @psalm-internal */
final class CastService implements CastServiceInterface
{

    /** @throws ExceptionInterface */
    public function getWallet(Wallet $object, bool $save = true): WalletModel
    {
        $wallet = $this->getModel($object);
        if (!($wallet instanceof WalletModel)) {
            $wallet = $wallet->getAttribute('wallet');
            assert($wallet instanceof WalletModel);
        }

        return $wallet;
    }

    /** @param Model|Wallet $object */
    public function getHolder($object): Model
    {
        return $this->getModel($object instanceof WalletModel ? $object->holder : $object);
    }

    public function getModel(object $object): Model
    {
        assert($object instanceof Model);

        return $object;
    }
}
