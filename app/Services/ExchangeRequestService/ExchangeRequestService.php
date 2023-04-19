<?php

namespace App\Services\ExchangeRequestService;

use App\Models\User;

interface ExchangeRequestService
{
    public function handle(User $user, array $data): bool;
}
