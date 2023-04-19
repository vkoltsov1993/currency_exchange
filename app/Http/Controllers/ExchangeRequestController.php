<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExchangeCurrencyRequest;
use App\Models\User;
use Illuminate\Http\Request;

class ExchangeRequestController extends Controller
{
    public function exchange(int $userId, ExchangeCurrencyRequest $request)
    {
        dd($request->validated());
        $user = User::findOrFail($userId);
        dd($user->wallets);
    }
}
