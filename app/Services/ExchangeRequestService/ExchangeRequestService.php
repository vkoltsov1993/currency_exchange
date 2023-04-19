<?php

namespace App\Services\ExchangeRequestService;

use App\Models\ExchangeRequest;
use App\Models\User;

interface ExchangeRequestService
{
    /**
     * @param User $user
     * @param array $data
     * @return ExchangeRequest
     */
    public function store(User $user, array $data): ExchangeRequest;

    public function apply(ExchangeRequest $exchangeRequest, User $user): bool;
}
