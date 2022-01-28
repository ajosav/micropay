<?php

namespace App\Services\Wallet;

use App\Contracts\MathServiceInterface;
use App\Contracts\AssistantServiceInterface;
use App\Services\Dto\TransactionDtoInterface;

final class AssistantService implements AssistantServiceInterface
{
    private MathServiceInterface $mathService;

    public function __construct(MathServiceInterface $mathService)
    {
        $this->mathService = $mathService;
    }

    public function getReferences(array $objects): array
    {
        return array_map(static fn ($object): string => $object->getReference(), $objects);
    }

    /**
     * @param non-empty-array<TransactionDtoInterface> $transactions
     *
     * @return array<int, string>
     */
    public function getSums(array $transactions): array
    {
        $amounts = [];
        foreach ($transactions as $transaction) {
            if ($transaction->isConfirmed()) {
                $amounts[$transaction->getWalletId()] = 
                    ($amounts[$transaction->getWalletId()] ?? 0 + $transaction->getAmount());
            }
        }

        return array_filter(
            $amounts,
            fn (string $amount): bool => $this->mathService->compare($amount, 0) !== 0
        );
    }
}
