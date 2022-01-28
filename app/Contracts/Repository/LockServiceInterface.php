<?php

namespace App\Contracts\Repository;

use App\Exceptions\LockProviderNotFoundException;

interface LockServiceInterface
{
    /**
     * @throws LockProviderNotFoundException
     *
     * @return mixed
     */
    public function block(string $key, callable $callback);
}
