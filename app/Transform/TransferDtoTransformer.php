<?php
namespace App\Transform;

use App\Services\Dto\TransferDtoInterface;
use App\Transform\TransferDtoTransformerInterface;

final class TransferDtoTransformer implements TransferDtoTransformerInterface
{
    public function extract(TransferDtoInterface $dto): array
    {
        return [
            'reference' => $dto->getReference(),
            'deposit_id' => $dto->getDepositId(),
            'withdraw_id' => $dto->getWithdrawId(),
            'status' => $dto->getStatus(),
            'from_type' => $dto->getFromType(),
            'from_id' => $dto->getFromId(),
            'to_type' => $dto->getToType(),
            'to_id' => $dto->getToId(),
            'created_at' => $dto->getCreatedAt(),
            'updated_at' => $dto->getUpdatedAt(),
        ];
    }
}
