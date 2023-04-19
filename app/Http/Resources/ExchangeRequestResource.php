<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExchangeRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'currency_give' => $this->currency_give,
            'amount_give' => $this->amount_give,
            'currency_get' => $this->currency_get,
            'amount_get' => $this->amount_get,
        ];
    }
}
