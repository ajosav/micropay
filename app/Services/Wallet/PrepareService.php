<?php
namespace App\Services\Wallet;

use App\Models\Transfer;
use App\Contracts\Wallet;
use App\Models\Transaction;
use App\Facades\MicropayUtil;
use App\Services\Dto\TransactionDto;
use App\Exceptions\MicropayException;
use App\Services\Dto\TransferLazyDto;
use App\Contracts\CastServiceInterface;
use App\Contracts\MathServiceInterface;
use App\Contracts\PrepareServiceInterface;
use App\Services\Dto\TransactionDtoInterface;
use App\Services\Dto\TransferLazyDtoInterface;

final class PrepareService implements PrepareServiceInterface
{
    private CastServiceInterface $castService;
    private MathServiceInterface $mathService;

    public function __construct(CastServiceInterface $castService, MathServiceInterface $mathService) 
    {
        $this->castService = $castService;
        $this->mathService = $mathService;
    }

    /**
     * @throws AmountInvalid
     */
    public function deposit(Wallet $wallet, string $amount, ?array $meta, bool $confirmed = true): TransactionDtoInterface
    {
        $this->checkPositive($amount);
        $transaction_reference = MicropayUtil::generateReference(Transaction::class, 'reference', 'trans_');
        $payable = $this->castService->getHolder($wallet);
        $payable_type = $payable->getMorphClass();
        $payable_id = $payable->getKey();

        return new TransactionDto(
            $transaction_reference,
            $payable_type,
            $payable_id,
            $this->castService->getWallet($wallet)->getKey(),
            Transaction::TYPE_DEPOSIT,
            $amount,
            $confirmed,
            $meta
        );
    }

    /**
     * @throws AmountInvalid
     */
    public function withdraw(Wallet $wallet, string $amount, ?array $meta, bool $confirmed = true): TransactionDtoInterface
    {
        $this->checkPositive($amount);
        $transaction_reference = MicropayUtil::generateReference(Transaction::class, 'reference', 'trans_');
        $payable = $this->castService->getHolder($wallet);
        $payable_type = $payable->getMorphClass();
        $payable_id = $payable->getKey();

        return new TransactionDto(
            $transaction_reference,
            $payable_type,
            $payable_id,
            $this->castService->getWallet($wallet)->getKey(),
            Transaction::TYPE_WITHDRAW,
            $amount,
            $confirmed,
            $meta
        );
    }

    /**
     * @param float|int|string $amount
     *
     * @throws AmountInvalid
     */
    public function transferLazy(Wallet $from, Wallet $to, string $status, $amount, ?array $meta = null): TransferLazyDtoInterface
    {
        // $transfer_reference = MicropayUtil::generateReference(Transfer::class, 'reference', 'trf_');
        $depositAmount = $this->mathService->compare($amount, 0) === -1 ? '0' : $amount;

        return new TransferLazyDto(
            $from,
            $to,
            $this->withdraw($from, $depositAmount, $meta),
            $this->deposit($to, $depositAmount, $meta),
            $status
        );
    }

    public function checkPositive($amount): void
    {
        if ($this->mathService->compare($amount, 0) === -1) {
            throw new MicropayException('Transaction amount cannot be negative');
        }
    }
}
