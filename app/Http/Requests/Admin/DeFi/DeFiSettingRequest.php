<?php

namespace App\Http\Requests\Admin\DeFi;

use Illuminate\Foundation\Http\FormRequest;

class DeFiSettingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'portfolio.wallet_name' => 'required',
            'portfolio.wallet_id'   => 'required',
            //            'contract.analyses_wallet' => 'required',
        ];
    }
}
