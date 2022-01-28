<?php

namespace App\Contracts\Repository;

interface TranslatorServiceInterface
{
    public function get(string $key): string;
}
