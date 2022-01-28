<?php

namespace App\Repository;

use App\Models\Transfer;
use App\Services\Dto\TransferDtoInterface;
use App\Transform\TransferDtoTransformerInterface;
use App\Contracts\Repository\TransferRepositoryInterface;

final class TransferRepository implements TransferRepositoryInterface
{
    private TransferDtoTransformerInterface $transformer;

    private Transfer $transfer;

    public function __construct(
        TransferDtoTransformerInterface $transformer,
        Transfer $transfer
    ) {
        $this->transformer = $transformer;
        $this->transfer = $transfer;
    }

    public function insert(array $objects): void
    {
        $values = array_map(fn (TransferDtoInterface $dto): array => $this->transformer->extract($dto), $objects);
        $this->transfer->newQuery()->insert($values);
    }

    public function insertOne(TransferDtoInterface $dto): Transfer
    {
        $attributes = $this->transformer->extract($dto);
        $instance = $this->transfer->newInstance($attributes);
        $instance::withoutEvents(static fn () => $instance->save());

        return $instance;
    }

    /** @return Transfer[] */
    public function findBy($references): array
    {
        return $this->transfer->newQuery()
            ->whereIn('reference', $references)
            ->get()
            ->all()
        ;
    }
}
