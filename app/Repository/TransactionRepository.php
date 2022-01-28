<?php

namespace App\Repository;

use App\Models\Transaction;
use App\Services\Dto\TransactionDtoInterface;
use App\Transform\TransactionDtoTransformerInterface;
use App\Contracts\Repository\TransactionRepositoryInterface;
use App\Facades\MicropayUtil;

final class TransactionRepository implements TransactionRepositoryInterface
{
    private TransactionDtoTransformerInterface $transformer;
    private Transaction $transaction;

    public function __construct(
        TransactionDtoTransformerInterface $transformer,
        Transaction $transaction
    ) {
        $this->transformer = $transformer;
        $this->transaction = $transaction;
    }

    /**
     * @param non-empty-array<int|string, TransactionDtoInterface> $objects
     */
    public function insert(array $objects): void
    {
        $values = [];
        foreach ($objects as $object) {
            $values[] = array_map(
                fn ($value) => is_array($value) ? MicropayUtil::encode($value) : $value,
                $this->transformer->extract($object)
            );
        }

        $this->transaction->newQuery()->insert($values);
    }

    public function insertOne(TransactionDtoInterface $dto): Transaction
    {
        $attributes = $this->transformer->extract($dto);
        $instance = $this->transaction->newInstance($attributes);
        $instance->save();
        $instance->refresh();
        // $instance::withoutEvents(static fn () => $instance->save());

        return $instance;
    }

    /** @return Transaction[] */
    public function findBy($query): array
    {
        return $this->transaction->newQuery()
            ->whereIn('reference', $query)
            ->get()
            ->all()
        ;
    }
}
