<?php

namespace App\Transform;

use App\Services\Dto\TransactionDtoInterface;

interface TransactionDtoTransformerInterface
{
    public function extract(TransactionDtoInterface $dto): array;
}
