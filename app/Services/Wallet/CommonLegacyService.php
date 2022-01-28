<?php

namespace App\Services\Wallet;

use App\Models\Transfer;
use App\Contracts\Wallet;
use App\Models\Transaction;
use App\Facades\MicropayUtil;
use App\Services\Dto\TransferDto;
use Illuminate\Support\Facades\DB;
use App\Contracts\AtmServiceInterface;
use App\Exceptions\ExceptionInterface;
use App\Contracts\CastServiceInterface;
use App\Contracts\PrepareServiceInterface;
use App\Exceptions\RecordNotFoundException;
use App\Contracts\AssistantServiceInterface;
use App\Contracts\RegulatorServiceInterface;
use App\Exceptions\TransactionFailedException;
use App\Services\Dto\TransferLazyDtoInterface;
use App\Exceptions\LockProviderNotFoundException;
use Illuminate\Database\RecordsNotFoundException;

class CommonLegacyService
{
    private AtmServiceInterface $atmService;
    private CastServiceInterface $castService;
    private AssistantServiceInterface $assistantService;
    private PrepareServiceInterface $prepareService;
    private RegulatorServiceInterface $regulatorService;

    public function __construct(
        CastServiceInterface $castService,
        AssistantServiceInterface $satisfyService,
        PrepareServiceInterface $prepareService,
        RegulatorServiceInterface $regulatorService,
        AtmServiceInterface $atmService
    ) {
        $this->atmService = $atmService;
        $this->castService = $castService;
        $this->assistantService = $satisfyService;
        $this->prepareService = $prepareService;
        $this->regulatorService = $regulatorService;
    }

    /**
     * @param int|string $amount
     *
     * @throws LockProviderNotFoundException
     * @throws RecordNotFoundException
     * @throws RecordsNotFoundException
     * @throws TransactionFailedException
     * @throws ExceptionInterface
     */
    public function forceTransfer(Wallet $from, Wallet $to, $amount, ?array $meta = null, string $status = Transfer::STATUS_TRANSFER): Transfer
    {
        $transferLazyDto = $this->prepareService->transferLazy($from, $to, $status, $amount, $meta);
        $transfers = $this->applyTransfers([$transferLazyDto]);

        return current($transfers);
    }

    /**
     * @param non-empty-array<TransferLazyDtoInterface> $objects
     *
     * @throws LockProviderNotFoundException
     * @throws RecordNotFoundException
     * @throws RecordsNotFoundException
     * @throws TransactionFailedException
     * @throws ExceptionInterface
     *
     * @return non-empty-array<Transfer>
     */
    public function applyTransfers(array $objects): array
    {
        return DB::transaction(function () use ($objects): array {
            $wallets = [];
            $operations = [];
            foreach ($objects as $object) {
                $fromWallet = $this->castService->getWallet($object->getFromWallet());
                $wallets[$fromWallet->getKey()] = $fromWallet;

                $toWallet = $this->castService->getWallet($object->getToWallet());
                $wallets[$toWallet->getKey()] = $toWallet;

                $operations[] = $object->getWithdrawDto();
                $operations[] = $object->getDepositDto();
            }

            $transactions = $this->applyTransactions($wallets, $operations);

            $transfers = [];
            foreach ($objects as $object) {
                $withdraw = $transactions[$object->getWithdrawDto()->getReference()] ?? null;
                assert($withdraw !== null);

                $deposit = $transactions[$object->getDepositDto()->getReference()] ?? null;
                assert($deposit !== null);
                $transaction_reference = MicropayUtil::generateReference(Transaction::class, 'reference', 'trans_');
                $fromModel = $this->castService->getModel($object->getFromWallet());
                $toModel = $this->castService->getModel($object->getToWallet());
            
                $transfers[] = new TransferDto(
                    $transaction_reference,
                    $deposit->getKey(),
                    $withdraw->getKey(),
                    $object->getStatus(),
                    $fromModel->getMorphClass(),
                    $fromModel->getKey(),
                    $toModel->getMorphClass(),
                    $toModel->getKey()
                );
            }

            return $this->atmService->makeTransfers($transfers);
        });
    }

    /**
     * @param float|int|string $amount
     *
     * @throws LockProviderNotFoundException
     * @throws RecordNotFoundException
     */
    public function makeTransaction(Wallet $wallet, string $type, $amount, ?array $meta, bool $confirmed = true): Transaction
    {
        assert(in_array($type, [Transaction::TYPE_DEPOSIT, Transaction::TYPE_WITHDRAW], true));

        if ($type === Transaction::TYPE_DEPOSIT) {
            $dto = $this->prepareService->deposit($wallet, (string) $amount, $meta, $confirmed);
        } else {
            $dto = $this->prepareService->withdraw($wallet, (string) $amount, $meta, $confirmed);
        }

        $transactions = $this->applyTransactions(
            [$dto->getWalletId() => $wallet],
            [$dto],
        );

        return current($transactions);
    }

    public function applyTransactions(array $wallets, array $objects): array
    {
        $transactions = $this->atmService->makeTransactions($objects); // q1
        $totals = $this->assistantService->getSums($objects);

        foreach ($totals as $walletId => $total) {
            $wallet = $wallets[$walletId] ?? null;
            assert($wallet !== null);

            $object = $this->castService->getWallet($wallet);

            $this->regulatorService->increase($object, $total);
        }

        return $transactions;
    }
}
