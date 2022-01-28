<?php

namespace App\Contracts\Repository;

use App\Models\Wallet;

interface WalletRepositoryInterface {

    public function create(array $attributes): Wallet;

    public function findById(int $id): ?Wallet;

    public function findByReference(string $reference): ?Wallet;
    
    public function findByHolder(string $holder_type, string $holder_id): ?Wallet;
    
    public function findByWalletUniqueId(string $walet_id): ?Wallet;
    
    public function findByName(string $holder_type, $holder_id, string $name): ?Wallet;

    /** @throws ModelNotFoundException */
    public function getById(int $id): Wallet;

    /** @throws ModelNotFoundException */
    public function getByWalletUniqueId(string $walet_id): Wallet;

    /** @throws ModelNotFoundException */
    public function getByReference(string $reference): Wallet;

    public function getByName(string $holder_type, $holder_id, string $name): Wallet;
}