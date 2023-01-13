<?php

namespace App\Http\Requests\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class FundWalletRequest extends FormRequest
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
        $validate_data = [
            'amount' => 'required|numeric|min:0',
            'reference' => 'nullable'
        ];

        if($this->routeIs('wallet.fund_wallet')){
            $validate_data['wallet_id'] = 'required|string';
        }

        return $validate_data;
        
    }
}
