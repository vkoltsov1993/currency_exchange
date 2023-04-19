<?php

namespace App\Http\Controllers;

use App\Exceptions\UserDoesNotHaveWalletException;
use App\Http\Requests\ExchangeCurrencyRequest;
use App\Http\Resources\ExchangeRequestResource;
use App\Models\User;
use App\Services\ExchangeRequestService\ExchangeRequestService;
use Illuminate\Http\Request;

class ExchangeRequestController extends Controller
{
    public function store(int $userId, ExchangeCurrencyRequest $request, ExchangeRequestService $exchangeRequestService)
    {
        try {
            $data = $request->validated();
            $user = User::findOrFail($userId);
            $exchangeRequest = $exchangeRequestService->handle($user, $data);
            return (new ExchangeRequestResource($exchangeRequest))
                ->response()
                ->setStatusCode(201);
        } catch (UserDoesNotHaveWalletException $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'error' => "Server error",
            ], 422);
        }
    }
}
