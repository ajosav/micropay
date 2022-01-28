<?php

namespace App\Http\Middleware;

use App\Facades\JwtUtil;
use Closure;
use Illuminate\Http\Request;

class JwtAuthorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        JwtUtil::authenticate();
        return $next($request);
    }
}
