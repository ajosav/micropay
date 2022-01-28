<?php

namespace App\Facades;

use App\Helpers\MicropayUtil as Util;
use Illuminate\Support\Facades\Facade;

class MicropayUtil extends Facade
{
    public static function getFacadeAccessor()
    {
        return Util::class;
    }
}