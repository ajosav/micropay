<?php

namespace App\Providers;

use App\Repository\LockService;
use App\Services\Dto\TransferDto;
use App\Services\Wallet\AtmService;
use App\Repository\WalletRepository;
use App\Services\Dto\TransactionDto;
use App\Services\Wallet\CastService;
use App\Services\Wallet\MathService;
use App\Services\Dto\TransferLazyDto;
use App\Contracts\AtmServiceInterface;
use App\Services\Wallet\AtomicService;
use App\Services\Wallet\WalletService;
use App\Contracts\CastServiceInterface;
use App\Contracts\MathServiceInterface;
use App\Services\Wallet\PrepareService;
use App\Services\Wallet\StorageService;
use Illuminate\Support\ServiceProvider;
use App\Contracts\AtomicServiceInterface;
use App\Contracts\WalletServiceInterface;
use App\Repository\TransactionRepository;
use App\Services\Wallet\AssistantService;
use App\Services\Wallet\RegulatorService;
use App\Transform\TransferDtoTransformer;
use App\Contracts\PrepareServiceInterface;
use App\Services\Dto\TransferDtoInterface;
use App\Services\Wallet\BookkeeperService;
use App\Services\Wallet\TranslatorService;
use App\Contracts\AssistantServiceInterface;
use App\Contracts\RegulatorServiceInterface;
use App\Services\Wallet\CommonLegacyService;
use App\Transform\TransactionDtoTransformer;
use App\Contracts\BookkeeperServiceInterface;
use App\Services\Dto\TransactionDtoInterface;
use App\Services\Dto\TransferLazyDtoInterface;
use App\Contracts\Repository\LockServiceInterface;
use App\Transform\TransferDtoTransformerInterface;
use App\Contracts\Repository\StorageServiceInterface;
use App\Transform\TransactionDtoTransformerInterface;
use App\Contracts\Repository\WalletRepositoryInterface;
use App\Contracts\Repository\TranslatorServiceInterface;
use App\Contracts\Repository\TransactionRepositoryInterface;
use App\Contracts\Repository\TransferRepositoryInterface;
use App\Repository\TransferRepository;

class WalletServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $configure = config('wallet', []);

        app()->singleton(AssistantServiceInterface::class, AssistantService::class);
        app()->singleton(AtmServiceInterface::class, AtmService::class);
        app()->singleton(AtomicServiceInterface::class, AtomicService::class);
        app()->singleton(BookkeeperServiceInterface::class, BookkeeperService::class);
        app()->singleton(CastServiceInterface::class, CastService::class);
        app()->singleton(LockServiceInterface::class, LockService::class);
        app()->singleton(MathServiceInterface::class, MathService::class);
        app()->singleton(PrepareServiceInterface::class, PrepareService::class);
        app()->singleton(TransactionDtoInterface::class, TransactionDto::class);
        app()->singleton(TransactionDtoTransformerInterface::class, TransactionDtoTransformer::class);
        app()->singleton(TransactionRepositoryInterface::class, TransactionRepository::class);
        app()->singleton(TransferDtoInterface::class, TransferDto::class);
        app()->singleton(TransferDtoTransformerInterface::class, TransferDtoTransformer::class);
        app()->singleton(TransferLazyDtoInterface::class, TransferLazyDto::class);
        app()->singleton(TransferRepositoryInterface::class, TransferRepository::class);
        app()->singleton(TranslatorServiceInterface::class, TranslatorService::class);
        app()->singleton(StorageServiceInterface::class, StorageService::class);
        app()->singleton(RegulatorServiceInterface::class, RegulatorService::class);
        app()->singleton(WalletRepositoryInterface::class, WalletRepository::class);
        app()->singleton(WalletServiceInterface::class, WalletService::class);
        app()->singleton(CommonLegacyService::class);
        $this->bindObjects($configure);
    }

    private function bindObjects(array $configure): void
    {
        $this->app->bind(Transaction::class, $configure['transaction']['model'] ?? null);
        $this->app->bind(Transfer::class, $configure['transfer']['model'] ?? null);
        $this->app->bind(Wallet::class, $configure['wallet']['model'] ?? null);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
