<?php

namespace App\Contracts\Repository;


interface StorageServiceInterface
{
    public function flush(): bool;

    public function missing(string $key): bool;

    public function get(string $key): string;

    /** @param float|int|string $value */
    public function sync(string $key, $value): bool;

    public function increase(string $key, $value): string;
}
