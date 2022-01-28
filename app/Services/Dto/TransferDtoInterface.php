<?php
namespace App\Services\Dto;

use DateTimeImmutable;

interface TransferDtoInterface
{
    public function getReference(): string;

    public function getDepositId(): int;

    public function getWithdrawId(): int;

    public function getStatus(): string;

    public function getFromType(): string;

    public function getFromId(): string;

    public function getToType(): string;

    public function getToId(): string;

    public function getCreatedAt(): DateTimeImmutable;

    public function getUpdatedAt(): DateTimeImmutable;
}
