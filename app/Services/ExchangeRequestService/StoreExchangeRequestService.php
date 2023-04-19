<?php

namespace App\Services\ExchangeRequestService;

use App\Exceptions\ExchangeRequestHasBeenAlreadyAppliedException;
use App\Exceptions\UserDoesNotHaveEnoughMoneyException;
use App\Exceptions\UserDoesNotHaveWalletException;
use App\Models\ExchangeFee;
use App\Models\ExchangeRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StoreExchangeRequestService implements ExchangeRequestService
{
    private string $currencyGive;
    private string $currencyGet;
    private float $amountGive;
    private float $amountGet;
    private User $user;

    /**
     * @param User $user
     * @param array $data
     * @return ExchangeRequest
     * @throws UserDoesNotHaveWalletException
     */
    public function store(User $user, array $data): ExchangeRequest
    {
        $this->currencyGive = $data['currency_give'];
        $this->currencyGet = $data['currency_get'];
        $this->amountGive = $data['amount_give'];
        $this->amountGet = $data['amount_get'];
        $this->user = $user;

        $this->isUserHasWallet();

        return ExchangeRequest::create([
            'user_id' => $this->user->id,
            'currency_give' => $this->currencyGive,
            'currency_get' => $this->currencyGet,
            'amount_give' => $this->amountGive,
            'amount_get' => $this->amountGet,
        ]);
    }

    /**
     * @return void
     * @throws UserDoesNotHaveWalletException
     */
    private function isUserHasWallet(): void
    {
        $usersWalletCurrencies = $this->user->wallets()
            ->pluck('currency')
            ->toArray();

        $currentCurrencies = [$this->currencyGet, $this->currencyGive];
        $notExistsCurrencies = array_diff($currentCurrencies, $usersWalletCurrencies);
        if (! empty($notExistsCurrencies)) {
            $errorMessage = "User doesn't have current currencies: ";
            $errorMessage .= implode(', ', $notExistsCurrencies);
            throw new UserDoesNotHaveWalletException($errorMessage, 422);
        }
    }

    /**
     * @param ExchangeRequest $exchangeRequest
     * @param User $user
     * @return bool
     * @throws ExchangeRequestHasBeenAlreadyAppliedException
     * @throws UserDoesNotHaveEnoughMoneyException
     */
    public function apply(ExchangeRequest $exchangeRequest, User $user): bool
    {
        if ($exchangeRequest->is_apply) {
            $errorMessage = "Exchange Request id:$exchangeRequest->id has been already applied";
            throw new ExchangeRequestHasBeenAlreadyAppliedException($errorMessage, 422);
        }
        $exchangeCurrencyGive = $exchangeRequest->currency_give;
        $exchangeAmountGive = (float)$exchangeRequest->amount_give;
        $availableMoney = (float)$user->wallets()
            ->where('currency', $exchangeCurrencyGive)
            ->value('amount');

        if ($availableMoney < $exchangeAmountGive) {
            $errorMessage = "User doesn't have enough money in wallet.";
            $errorMessage .= "\nThe are $availableMoney $exchangeCurrencyGive available";
            throw new UserDoesNotHaveEnoughMoneyException($errorMessage, 422);
        }
        DB::beginTransaction();

        try {
            $exchangeRequest->is_apply = true;
            $exchangeRequest->save();
            ExchangeFee::create([
                'exchange_request_id' => $exchangeRequest->id,
                'currency' => $exchangeCurrencyGive,
                'fee' => $exchangeAmountGive * 0.02,
            ]);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        return true;
    }
}
