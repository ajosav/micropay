<?php

namespace App\Transform;

use App\Services\Dto\TransactionDtoInterface;

final class TransactionDtoTransformer implements TransactionDtoTransformerInterface
{
    public function extract(TransactionDtoInterface $dto): array
    {
        return [
            'reference' => $dto->getReference(),
            'payable_type' => $dto->getPayableType(),
            'payable_id' => $dto->getPayableId(),
            'wallet_id' => $dto->getWalletId(),
            'referece' => $dto->getReference(),
            'type' => $dto->getType(),
            'amount' => $dto->getAmount(),
            'confirmed' => $dto->isConfirmed(),
            'meta' => $dto->getMeta(),
            'created_at' => $dto->getCreatedAt(),
            'updated_at' => $dto->getUpdatedAt(),
        ];
    }
}
