<?php

namespace App\Services\Dto;

use DateTimeImmutable;

interface TransactionDtoInterface
{
    public function getReference(): string;

    public function getPayableType(): string;

    public function getPayableId(): string;

    public function getWalletId(): string;

    public function getType(): string;

    public function getAmount(): string;

    public function isConfirmed(): bool;

    public function getMeta(): ?array;

    public function getCreatedAt(): DateTimeImmutable;

    public function getUpdatedAt(): DateTimeImmutable;
}