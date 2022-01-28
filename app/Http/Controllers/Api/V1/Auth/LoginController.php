<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Facades\JwtUtil;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\User\AuthUserResource;
use App\Traits\AuthenticateUserTrait;

class LoginController extends Controller
{
    use AuthenticateUserTrait;

    protected $username;
    
    public function __construct()
    {
        $this->username = $this->findusername();
    }

    public function findUserName()
    {
        $login = request()->username;
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';
        request()->merge([$fieldType => $login]);
        return $fieldType;
    }

    public function username()
    {
        return $this->username;
    }

    public function login(LoginRequest $request)
    {
        $user = $this->authenticate($request);

        return (new AuthUserResource($user))->additional([
            'message' => 'You are logged in!',
            'status' => "success"
        ]);
    }

    public function refreshToken()
    {
        $user = JwtUtil::refreshToken();
        return (new AuthUserResource($user))->additional([
            'message' => 'Token successfully refreshed',
            'status' => "success"
        ]);
    }
}