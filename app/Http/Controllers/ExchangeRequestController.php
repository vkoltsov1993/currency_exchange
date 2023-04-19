<?php

namespace App\Http\Controllers;

use App\Exceptions\ExchangeRequestHasBeenAlreadyAppliedException;
use App\Exceptions\UserDoesNotHaveEnoughMoneyException;
use App\Exceptions\UserDoesNotHaveWalletException;
use App\Http\Requests\ExchangeCurrencyRequest;
use App\Http\Requests\ExchangeRequestApplyRequest;
use App\Http\Resources\ExchangeRequestResource;
use App\Models\ExchangeRequest;
use App\Models\User;
use App\Repositories\ExchangeRequestRepository;
use App\Services\ExchangeRequestService\ExchangeRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExchangeRequestController extends Controller
{
    public function store(int $userId, ExchangeCurrencyRequest $request, ExchangeRequestService $exchangeRequestService)
    {
        try {
            $data = $request->validated();
            $user = User::findOrFail($userId);
            $exchangeRequest = $exchangeRequestService->store($user, $data);
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

    public function list(ExchangeRequestRepository $exchangeRequestRepository)
    {
        return ExchangeRequestResource::collection($exchangeRequestRepository->all());
    }

    public function apply(int $userId, ExchangeRequestApplyRequest $request, ExchangeRequestService $exchangeRequestService): JsonResponse
    {
        try {
            $user = User::find($userId);
            $data = $request->validated();
            $exchangeRequestId = (int)$data['exchange_request_id'];
            $exchangeRequest = ExchangeRequest::findOrFail($exchangeRequestId);
            $exchangeRequestService->apply($exchangeRequest, $user);
            return response()->json([
                'message' => "Exchange Request id:$exchangeRequest->id successfully applied",
            ]);
        } catch (UserDoesNotHaveEnoughMoneyException|ExchangeRequestHasBeenAlreadyAppliedException $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], $exception->getCode());
        }
    }
}
