<?php

namespace App\Helpers;

use App\Contracts\CastServiceInterface;
use Throwable;
use App\Contracts\Wallet;
use App\Enums\StatusCode;
use App\Exceptions\MicropayException;
use App\Contracts\MathServiceInterface;
use Illuminate\Database\Eloquent\Model;
use App\Contracts\Repository\TranslatorServiceInterface;

class MicropayUtil 
{
    public MathServiceInterface $mathService;
    public TranslatorServiceInterface $translatorService;
    public CastServiceInterface $castService;

    public function __construct(
        MathServiceInterface $mathService, 
        TranslatorServiceInterface $translatorService, 
        CastServiceInterface $castService
    ) {
        $this->mathService = $mathService;
        $this->translatorService = $translatorService;
        $this->castService = $castService;
    }
    public function generateReference($model, $field, ? string $prefix = '') : string
    {
        $reference = $prefix.mt_rand(10000000, 99999999999).time();
        // call the same function if the token exists already
        if ($this->tokenExist($model, $field, $reference)) {
            return $this->generateReference($model, $field);
        }
        return $reference;
    }

    private function tokenExist($model, $field, $reference) : bool
    {
        // query the database and return a boolean
        return $model::where($field, $reference)->exists();
    }

    public function encode(?array $data): ?string
    {
        try {
            return $data === null ? null : json_encode($data, JSON_THROW_ON_ERROR);
        } catch (Throwable $throwable) {
            return null;
        }
    }

    /**
     * @param float|int|string $amount
     *
     * @throws AmountInvalid
     */
    public function checkPositive($amount): void
    {
        if ($this->mathService->compare($amount, 0) === -1) {
            throw new MicropayException(
                $this->translatorService->get('Amount cannot be negative'),
                StatusCode::BAD_RESPONSE
            );
        }
    }

    /**
     * @param float|int|string $amount
     *
     * @throws MicropayException
     */
    public function checkPotential(Wallet $object, $amount, bool $allowZero = false): void
    {
        $wallet = $this->castService->getWallet($object, false);
        $balance = $this->mathService->add($wallet->getBalanceAttribute(), $wallet->getCreditAttribute());

        if (($this->mathService->compare($amount, 0) !== 0) && ($this->mathService->compare($balance, 0) === 0)) {
            throw new MicropayException(
                $this->translatorService->get('Insufficient Funds'),
                StatusCode::BAD_RESPONSE
            );
        }

        if (!$this->canWithdraw($balance, $amount, $allowZero)) {
            throw new MicropayException(
                $this->translatorService->get('Insufficient Funds'),
                StatusCode::BAD_RESPONSE
            );
        }
    }

    /**
     * @param float|int|string $balance
     * @param float|int|string $amount
     */
    public function canWithdraw($balance, $amount, bool $allowZero = false): bool
    {
        $mathService = app(MathServiceInterface::class);

        /**
         * Allow buying for free with a negative balance.
         */
        if ($allowZero && !$mathService->compare($amount, 0)) {
            return true;
        }

        return $mathService->compare($balance, $amount) >= 0;
    }
}