<?php

namespace App\Http\Requests\Auth;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\MicropayException;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Password as PasswordReset;

class PassswordResetRequest extends FormRequest
{
    public function prepareForValidation() {
        if ($this->filled('hash')) {
            try {
                $this->merge(['email' => decrypt($this->input('hash'))]);
            } catch (DecryptException $e) {
                throw new MicropayException('Invalid expiration timestamp', 400);
            }
        }
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => [ 'required',
                            'string',
                            'confirmed',
                            Password::defaults()
                        ]
        ];
    }

    public function resetPassword() 
    {
        $status = PasswordReset::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => $password
                ])->save();
    
                $user->setRememberToken(Str::random(60));
    
                event(new PasswordReset($user));
            }
        );

        return $status;
    }
}
