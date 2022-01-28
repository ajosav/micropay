<?php

namespace App\Http\Requests\Auth;

use Carbon\Carbon;
use Illuminate\Validation\Validator;
use App\Exceptions\MicropayException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerifyEmailRequest extends EmailVerificationRequest
{
    public function prepareForValidation() {
        $this->formatRequestInputs();
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->filled('id') && ! hash_equals((string) $this->input('id'),
                    (string) $this->user()->getKey())) {
            throw new MicropayException('Access Denied! Invalid Verification ID', 403);
        }

        if ($this->filled('hash') && ! hash_equals((string) $this->input('hash'),
                sha1($this->user()->getEmailForVerification()))) {
            throw new MicropayException('Access Denied! Token is invalid', 403);
        }

        if ($this->filled('expires') && (Carbon::now()->getTimestamp() > $this->input('expires')) ) {
            throw new MicropayException('Link Expired', 419);
        }
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
            'id' => 'required',
            'hash' => 'required',
            'expires' => 'required'
        ];
    }

    protected function formatRequestInputs()
    {
        if ($this->filled('expires')) {
            try {
                $this->merge(['expires' => decrypt($this->input('expires'))]);
            } catch (DecryptException $e) {
                throw new MicropayException('Invalid expiration timestamp', 400);
            }
        }
    }
}
