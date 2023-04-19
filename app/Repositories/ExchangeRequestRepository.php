<?php

namespace App\Repositories;

use App\Models\ExchangeRequest;
use Illuminate\Database\Eloquent\Collection;

class ExchangeRequestRepository
{
    /**
     * @param string $give
     * @param string $get
     * @return Collection
     */
    public function getAvailableRequestsByCurrencies(string $give, string $get): Collection
    {
        return ExchangeRequest::query()
            ->where('currency_give', $give)
            ->where('currency_get', $get)
            ->where('amount_give', '>', 0)
            ->get();
    }

    public function all(int $userIdExcept = null): Collection
    {
        $exchangeRequestBuilder = ExchangeRequest::query();
        if ($userIdExcept) {
            $exchangeRequestBuilder->whereNot('user_id', $userIdExcept);
        }
        return $exchangeRequestBuilder->get();
    }
}
