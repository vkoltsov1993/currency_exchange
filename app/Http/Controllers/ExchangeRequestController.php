<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExchangeCurrencyRequest;
use App\Models\User;
use App\Services\ExchangeRequestService\ExchangeRequestService;
use Illuminate\Http\Request;

class ExchangeRequestController extends Controller
{
    public function exchange(int $userId, ExchangeCurrencyRequest $request, ExchangeRequestService $exchangeRequestService)
    {
        $data = $request->validated();
        $user = User::findOrFail($userId);
        $exchangeRequestService->handle($user, $data);
    }
}
