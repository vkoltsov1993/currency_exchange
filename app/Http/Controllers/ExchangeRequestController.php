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
    public function __construct(
        private readonly ExchangeRequestService $exchangeRequestService,
    ) {
        //
    }

    public function store(ExchangeCurrencyRequest $request)
    {
        try {
            $data = $request->validated();
            $user = auth()->user();
            $exchangeRequest = $this->exchangeRequestService->store($user, $data);
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

    public function apply(ExchangeRequestApplyRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $data = $request->validated();
            $exchangeRequestId = (int)$data['exchange_request_id'];
            $exchangeRequest = ExchangeRequest::findOrFail($exchangeRequestId);
            $this->exchangeRequestService->apply($exchangeRequest, $user);
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
