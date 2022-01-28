<?php

namespace App\Http\Requests\Auth;

use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Http\FormRequest;

class PassswordResetLinkRequest extends FormRequest
{
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
            'email' => ['required', 'email']
        ];
    }

    public function getPasswordResetStatus()
    {
        return Password::sendResetLink(
            $this->only('email')
        );
    }
}
