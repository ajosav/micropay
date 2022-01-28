<?php

namespace App\Traits;

use App\Enums\StatusCode;
use App\Exceptions\MicropayException;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Lockout;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

trait AuthenticateUserTrait
{
    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(Request $request)
    {
        $this->ensureIsNotRateLimited($request);

        try {
            $this->loginWithCredentials($request);
        } catch (JWTException $e) {
            throw new MicropayException("Error Generating Token", StatusCode::BAD_RESPONSE, null, ["errorDetails" => $e->getMessage()]);
        }
        
        RateLimiter::clear($this->throttleKey($request));
        return auth()->user();
    }

    private function loginWithCredentials(Request $request) 
    {
        if (! auth()->attempt($this->credentials($request))) {
            RateLimiter::hit($this->throttleKey($request));

            throw ValidationException::withMessages([
                $this->username() => "Either the " . $this->username() . " or password provided does not match",
            ]);
        }
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(Request $request)
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey(Request $request)
    {
        $username = $this->username();
        return Str::lower($request->$username).'|'.$request->ip();
    }

    public function credentials(Request $request, array $credentials = [])
    {
        if(empty($credentials)) {
            return $request->only($this->username(), 'password');
        }
        return $credentials;
    }

    public function authenticated() 
    {

    }

    public function username() {
        return 'email';
    }
}