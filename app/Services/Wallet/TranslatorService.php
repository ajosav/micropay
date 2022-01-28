<?php

namespace App\Services\Wallet;

use Illuminate\Contracts\Translation\Translator;
use App\Contracts\Repository\TranslatorServiceInterface;

final class TranslatorService implements TranslatorServiceInterface
{
    private Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function get(string $key): string
    {
        $value = $this->translator->get($key);
        assert(is_string($value));

        return $value;
    }
}
