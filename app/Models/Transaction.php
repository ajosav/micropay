<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public const TYPE_WITHDRAW = 'withdraw';
    public const TYPE_DEPOSIT = 'deposit';

    protected $guarded = ['id'];
}
