<?php

namespace App\Facades;

use App\Helpers\JwtUtil as AuthUtil;
use Illuminate\Support\Facades\Facade;

class JwtUtil extends Facade
{
    public static function getFacadeAccessor()
    {
        return AuthUtil::class;
    }
}