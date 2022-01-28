<?php

namespace App\Contracts;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Model;

interface WalletServiceInterface
{
    public function create(Model $model, array $attributes): Wallet;

    public function findById(int $id): ?Wallet;

    public function findByReference(string $reference): ?Wallet;

    public function findByWalletUniqueId(string $walet_id): ?Wallet;

    public function findByholder(Model $model): ?Wallet;

    public function findByName(Model $model, string $name): ?Wallet;

    /** @throws ModelNotFoundException */
    public function getById(int $id): Wallet;

    /** @throws ModelNotFoundException */
    public function getByWalletUniqueId(string $walet_id): Wallet;

    /** @throws ModelNotFoundException */
    public function getByReference(string $reference): Wallet;

    public function getByName(Model $model, string $name): Wallet;

}