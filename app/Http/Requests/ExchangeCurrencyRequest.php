<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExchangeCurrencyRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'currency_give' => 'required',
            'amount_give' => 'required|decimal:0,2',
            'currency_get' => 'required',
            'amount_get' => 'required|decimal:0,2',
        ];
    }
}
