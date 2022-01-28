<?php

namespace App\Services\Dto;

use DateTimeImmutable;

/** @psalm-immutable */
final class TransactionDto implements TransactionDtoInterface
{
    private string $reference;

    private string $payableType;
    private string $payableId;

    private string $walletId;

    private string $type;

    private string $amount;

    private bool $confirmed;

    private ?array $meta;

    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $reference,
        string $payableType,
        string $payableId,
        string $walletId,
        string $type,
        string $amount,
        bool $confirmed,
        ?array $meta
    ) {
        $this->reference = $reference;
        $this->payableType = $payableType;
        $this->payableId = $payableId;
        $this->walletId = $walletId;
        $this->type = $type;
        $this->amount = $amount;
        $this->confirmed = $confirmed;
        $this->meta = $meta;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getPayableType(): string
    {
        return $this->payableType;
    }

    public function getPayableId(): string
    {
        return $this->payableId;
    }

    public function getWalletId(): string
    {
        return $this->walletId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function isConfirmed(): bool
    {
        return $this->confirmed;
    }

    public function getMeta(): ?array
    {
        return $this->meta;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
