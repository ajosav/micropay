<?php

namespace App\Transform;

use App\Services\Dto\TransferDtoInterface;

interface TransferDtoTransformerInterface
{
    public function extract(TransferDtoInterface $dto): array;
}
