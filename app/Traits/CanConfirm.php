<?php

namespace App\Traits;

use App\Enums\StatusCode;
use App\Models\Transaction;
use App\Facades\MicropayUtil;
use App\Exceptions\MicropayException;
use App\Exceptions\ExceptionInterface;
use App\Contracts\CastServiceInterface;
use App\Contracts\MathServiceInterface;
use App\Contracts\AtomicServiceInterface;
use App\Contracts\RegulatorServiceInterface;

trait CanConfirm
{
    public function confirm(Transaction $transaction, $status = 'successful'): bool
    {
        if ($transaction->type === Transaction::TYPE_WITHDRAW) {
            MicropayUtil::checkPotential(
                app(CastServiceInterface::class)->getWallet($this),
                app(MathServiceInterface::class)->negative($transaction->amount)
            );
        }

        return $this->forceConfirm($transaction, $status);
    }

    public function safeConfirm(Transaction $transaction): bool
    {
        try {
            return $this->confirm($transaction);
        } catch (ExceptionInterface $throwable) {
            return false;
        }
    }

    public function resetConfirm(Transaction $transaction, ? string $status = 'pending'): bool
    {
        return app(AtomicServiceInterface::class)->block($this, function () use ($transaction, $status) {
            if (!$transaction->confirmed) {
                throw new MicropayException("Transaction has not been confirmed", StatusCode::BAD_RESPONSE);
            }

            $wallet = app(CastServiceInterface::class)->getWallet($this);
            app(RegulatorServiceInterface::class)->decrease($wallet, $transaction->amount);

            return $transaction->update(['confirmed' => false, 'status' => $status]);
        });
    }

    public function safeResetConfirm(Transaction $transaction): bool
    {
        try {
            return $this->resetConfirm($transaction);
        } catch (ExceptionInterface $throwable) {
            return false;
        }
    }

    public function forceConfirm(Transaction $transaction, $status = 'successful'): bool
    {
        return app(AtomicServiceInterface::class)->block($this, function () use ($transaction, $status) {
            if ($transaction->confirmed) {
                throw new MicropayException("Transaction has already been confirmed", StatusCode::BAD_RESPONSE);
            }

            $wallet = app(CastServiceInterface::class)->getWallet($this);
            if ($wallet->getKey() !== $transaction->wallet_id) {
                throw new MicropayException("Wallet not found", StatusCode::NOT_FOUND);
            }

            app(RegulatorServiceInterface::class)->increase($wallet, $transaction->amount);

            return $transaction->update(['confirmed' => true, 'status' => $status]);
        });
    }
}
