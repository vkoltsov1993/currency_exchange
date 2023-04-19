<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeeReportRequest extends FormRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'date_from' => $this->date_from . ' 00:00:00',
            'date_to' => $this->date_to . ' 23:59:59',
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'date_from' => 'sometimes|date_format:Y-m-d H:i:s',
            'date_to' => 'sometimes|date_format:Y-m-d H:i:s',
        ];
    }
}
