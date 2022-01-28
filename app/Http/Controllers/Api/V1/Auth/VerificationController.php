<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyEmailRequest;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verifyEmail(VerifyEmailRequest $request) 
    {
        $request->fulfill();
        return response()->success('Email verification successful');
    }

    public function resendLink(Request $request)
    {
        if(!$request->user()->hasVerifiedEmail())
        {
            $request->user()->sendEmailVerificationNotification();
            return response()->success('Email verification link sent');
        }
        return response()->success('Email has already been verified');
    }
}
