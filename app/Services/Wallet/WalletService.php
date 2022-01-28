<?php

namespace App\Services\Wallet;

use App\Models\Wallet;
use App\Facades\MicropayUtil;
use Illuminate\Database\Eloquent\Model;
use App\Contracts\WalletServiceInterface;
use App\Contracts\Repository\WalletRepositoryInterface;

final class WalletService implements WalletServiceInterface
{
    private WalletRepositoryInterface $walletRepository;

    public function __construct(WalletRepositoryInterface $walletRepository) 
    {
        $this->walletRepository = $walletRepository;
    }

    public function create(Model $model, array $data): Wallet
    {
        $custom_data = [
            'holder_type' => $model->getMorphClass(),
            'holder_id' => $model->getKey(),
        ];

        if(!array_key_exists('reference', $data)) {
            $custom_data['reference'] = MicropayUtil::generateReference(Wallet::class, 'reference', 'mpay-');
        }
        
        $wallet = $this->walletRepository->create(array_merge(
            $data,
            $custom_data
        ));

        return $wallet;
    }

    public function findByReference(string $reference): ?Wallet
    {
        return $this->walletRepository->findByReference(
            $reference
        );
    
    }
    public function findByName(Model $model, string $name = 'default'): ?Wallet
    {
        return $this->walletRepository->findByName(
            $model->getMorphClass(),
            $model->getKey(),
            $name
        );
    }

    public function findByHolder(Model $model): ?Wallet
    {
        return $this->walletRepository->findByHolder(
            $model->getMorphClass(),
            $model->getKey()
        );
    }

    public function findByWalletUniqueId(string $wallet_id): ?Wallet
    {
        return $this->walletRepository->findByWalletUniqueId($wallet_id);
    }

    public function findById(int $id): ?Wallet
    {
        return $this->walletRepository->findById($id);
    }

    /** @throws ModelNotFoundException */
    public function getByReference(string $reference): Wallet
    {
        return $this->walletRepository->getByReference(
            $reference
        );
    }

    /** @throws ModelNotFoundException */
    public function getByName(Model $model, string $name = 'default'): Wallet
    {
        return $this->walletRepository->getByName(
            $model->getMorphClass(),
            $model->getKey(),
            $name
        );
    }
    
    /** @throws ModelNotFoundException */
    public function getByWalletUniqueId(string $wallet_id): Wallet
    {
        return $this->walletRepository->getByWalletUniqueId($wallet_id);
    }

    /** @throws ModelNotFoundException */
    public function getById(int $id): Wallet
    {
        return $this->walletRepository->getById($id);
    }
}
