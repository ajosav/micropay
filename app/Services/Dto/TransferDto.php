<?php

namespace App\Services\Dto;

use DateTimeImmutable;
use App\Services\Dto\TransferDtoInterface;

/** @psalm-immutable */
final class TransferDto implements TransferDtoInterface
{
    private string $reference;

    private string $depositId;
    private string $withdrawId;

    private string $status;

    private string $fromType;
    private string $fromId;

    private string $toType;
    private string $toId;

    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $reference,
        string $depositId,
        string $withdrawId,
        string $status,
        string $fromType,
        string $fromId,
        string $toType,
        string $toId
    ) {
        $this->reference = $reference;
        $this->depositId = $depositId;
        $this->withdrawId = $withdrawId;
        $this->status = $status;
        $this->fromType = $fromType;
        $this->fromId = $fromId;
        $this->toType = $toType;
        $this->toId = $toId;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getDepositId(): int
    {
        return $this->depositId;
    }

    public function getWithdrawId(): int
    {
        return $this->withdrawId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getFromType(): string
    {
        return $this->fromType;
    }

    public function getFromId(): string
    {
        return $this->fromId;
    }

    public function getToType(): string
    {
        return $this->toType;
    }

    public function getToId(): string
    {
        return $this->toId;
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
