<?php

namespace App\Contracts\Repository;

use App\Models\Transfer;
use App\Services\Dto\TransferDtoInterface;

interface TransferRepositoryInterface
{
    public function insert(array $objects): void;

    public function insertOne(TransferDtoInterface $dto): Transfer;

    /** @return Transfer[] */
    public function findBy($query): array;
}
