<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Facades\JwtUtil;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{

    public function authenticatedUser() 
    {
        $user = JwtUtil::getAuthenticatedUser();
        return (new UserResource($user))->additional([
            'status' => 'success',
            'message' => 'Successful'
        ]);
    }

    public function forgotPassword(Request $request) {
        $request->validate(['email' => 'required|email']);
        return $this->userService->sendPasswordResetLink($request);
    }


    public function resetPassword(Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required',
                            'string',
                            'confirmed',
                            Password::defaults()
            ]
        ]);
            
        return $this->userService->resetPassword($request);
        
    }

    public function logout() {
        if(auth()->check()) {
            auth()->logout();
            return response()->success('Session ended! Log out was successful');
        };
       return response()->errorResponse('You are not logged in', [], 401);
    }
}
