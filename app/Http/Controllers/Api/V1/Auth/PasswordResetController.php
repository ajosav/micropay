<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PassswordResetLinkRequest;
use App\Http\Requests\Auth\PassswordResetRequest;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    public function forgotPassword(PassswordResetLinkRequest $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = $request->getPasswordResetStatus();

        return $status === Password::RESET_LINK_SENT
                    ? response()->success(__($status))
                    : response()->errorResponse(__($status));
    }

    public function resetPassword(PassswordResetRequest $request)
    {
        $status = $request->resetPassword();

        return $status == Password::PASSWORD_RESET
                    ? response()->success(__($status))
                    : response()->errorResponse(__($status));
    }
}
