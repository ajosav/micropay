<?php

namespace App\Contracts;

use App\Models\Transfer;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Confirmable {

    public function confirm(Transaction $transaction): bool;

    public function safeConfirm(Transaction $transaction): bool;

    public function resetConfirm(Transaction $transaction): bool;

    public function safeResetConfirm(Transaction $transaction): bool;

    public function forceConfirm(Transaction $transaction): bool;
}
