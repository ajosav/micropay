<?php

namespace App\Helpers;

use App\Enums\StatusCode;
use App\Exceptions\MicropayException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

class JwtUtil 
{

    public function authenticate() 
    {
        try {
            JWTAuth::parseToken()->authenticate();
            if (! $user = auth()->user())
                throw new MicropayException("User not found", StatusCode::NOT_FOUND);
    
        } catch (TokenExpiredException $e) {
            throw new MicropayException("Token Expired", StatusCode::UNAUTHORIZED, null, ["token" => $e->getMessage()]);
        } catch (TokenInvalidException $e) {
            throw new MicropayException("Invalid Token", StatusCode::UNAUTHORIZED, null, ["token" => $e->getMessage()]);
        } catch (JWTException $e) {
            throw new MicropayException("Token Absent", StatusCode::UNAUTHORIZED, null, ["token" => $e->getMessage()]);
        }
        return $user;
    }

    public function refreshToken() 
    {
        try {
            if(!auth()->refresh())
                throw new MicropayException("Unable to refresh token", StatusCode::BAD_RESPONSE);
                
            $user = auth()->user();
        } catch(TokenBlacklistedException $e) {
            throw new MicropayException('Token has already been refreshed and invalidated', StatusCode::BAD_RESPONSE, null, ["token" => $e->getMessage()]);
        } catch (TokenInvalidException $e) {
            throw new MicropayException('Token has already been refreshed and invalidated', StatusCode::BAD_RESPONSE, null, ["token" => $e->getMessage()]);            
        } catch (JWTException $e) {
            throw new MicropayException('Please pass a bearer token', StatusCode::BAD_RESPONSE, null, ["token" => $e->getMessage()]);
        }
        
        return $user;
    }

    public function getAuthenticatedUser()
    {
        return $this->authenticate();
    }

    public function respondWithToken($user = null)
    {
        $token = JWTAuth::fromUser($user ?? auth()->user());
        return [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
    }

}