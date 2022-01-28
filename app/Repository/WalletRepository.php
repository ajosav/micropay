<?php

namespace App\Repository;

use App\Models\Wallet;
use App\Contracts\Repository\WalletRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WalletRepository implements WalletRepositoryInterface
{
    protected $wallet;

    public function __construct(Wallet $wallet)
    {
        $this->wallet = $wallet;
    }

    public function create(array $attributes): Wallet
    {
        $instance = $this->wallet->newInstance($attributes);
        $instance->save();
        $instance->refresh();
        // $instance::withoutEvents(static fn () => $instance->save());

        return $instance;
    }

    public function findById(int $id): ?Wallet
    {
        try {
            return $this->getById($id);
        } catch (ModelNotFoundException $modelNotFoundException) {
            return null;
        }
    }

    public function findByReference(string $reference): ?Wallet
    {
        return $this->getByReference($reference);
        
    
    }
    public function findByName($holder_type, $holder_id, string $name): ?Wallet
    {
        return $this->getByName($holder_type, $holder_id, $name);
        
    }
    
    public function findByHolder($holder_type, $holder_id): ?Wallet
    {
        return $this->getByHolder($holder_type, $holder_id);
        
    }

    public function findByWalletUniqueId(string $wallet_id): ?Wallet
    {
        return $this->getByWalletUniqueId($wallet_id);
    }
    
    /** @throws ModelNotFoundException */
    public function getById(int $id): Wallet
    {
        return $this->getBy(['id' => $id]);
    }

    /** @throws ModelNotFoundException */
    public function getByReference(string $reference): Wallet
    {
        return $this->getBy(['reference' => $reference]);
    
    }
    /** @throws ModelNotFoundException */
    public function getByName($holderType, $holderId, string $name): Wallet
    {
        return $this->getBy([
            'holder_type' => $holderType,
            'holder_id' => $holderId,
            'wallet_name' => $name,
        ]);
    }

    /** @throws ModelNotFoundException */
    public function getByWalletUniqueId(string $wallet_id): Wallet
    {
        return $this->getBy(['wallet_id' => $wallet_id]);
    }

    /** @param array<string, int|string> $attributes */
    private function getBy(array $attributes): Wallet
    {
        $wallet = $this->wallet->newQuery()->where($attributes)->firstOrFail();
        return $wallet;
    }

    private function getByHolder($holderType, $holderId): Wallet
    {
        return $this->getBy([
            'holder_type' => $holderType,
            'holder_id' => $holderId,
        ]);
    }
    
}