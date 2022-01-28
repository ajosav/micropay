<?php

namespace App\Contracts;

use App\Contracts\Wallet;
use Illuminate\Database\Eloquent\Model;
use App\Models\Wallet as WalletModel;

interface CastServiceInterface
{
    
    public function getWallet(Wallet $object, bool $save = true): WalletModel;

    /** @param Model|WalletModel $object */
    public function getHolder($object): Model;

    public function getModel(object $object): Model;
}