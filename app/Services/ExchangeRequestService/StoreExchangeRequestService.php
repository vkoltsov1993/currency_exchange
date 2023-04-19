<?php

namespace App\Services\ExchangeRequestService;

use App\Exceptions\AttemptToApplyOwnRequestException;
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
     * @throws AttemptToApplyOwnRequestException
     * @throws ExchangeRequestHasBeenAlreadyAppliedException
     * @throws UserDoesNotHaveEnoughMoneyException
     * @throws UserDoesNotHaveWalletException
     */
    public function apply(ExchangeRequest $exchangeRequest, User $user): bool
    {
        if ($exchangeRequest->is_apply) {
            $errorMessage = "Exchange Request id:$exchangeRequest->id has been already applied";
            throw new ExchangeRequestHasBeenAlreadyAppliedException($errorMessage, 422);
        }

        if ($exchangeRequest->user->id == $user->id) {
            $message = "You can't apply your exchange request";
            throw new AttemptToApplyOwnRequestException($message, 422);
        }

        $ownerGiveCurrency = $exchangeRequest->currency_give;
        $ownerGiveAmount = (float)$exchangeRequest->amount_give;
        $ownerGetCurrency = $exchangeRequest->currency_get;
        $ownerGetAmount = (float)$exchangeRequest->amount_get;

        $ownerAvailableMoney = (float)$exchangeRequest->user->wallets()
            ->where('currency', $ownerGiveCurrency)
            ->value('amount');

        if ($ownerAvailableMoney < $ownerGiveAmount) {
            $errorMessage = "Request owner doesn't have enough money";
            throw new UserDoesNotHaveEnoughMoneyException($errorMessage, 422);
        }

        $isApplierHaveWallet = $user->wallets()
            ->where('currency', $ownerGetCurrency)
            ->exists();

        if (! $isApplierHaveWallet) {
            $message = "You don't have $ownerGetCurrency wallet!";
            throw new UserDoesNotHaveWalletException($message, 422);
        }

        $applierAvailableMoney = (float)$user->wallets()
            ->where('currency', $ownerGetCurrency)
            ->value('amount');

        $fee = config('exchange.fee');
        $systemFee = $ownerGetAmount * $fee;

        if (($applierAvailableMoney + $systemFee) < $ownerGetAmount) {
            $errorMessage = "You don't have enough money";
            throw new UserDoesNotHaveEnoughMoneyException($errorMessage, 422);
        }

        DB::beginTransaction();

        try {
            $ownerGetWallet = $exchangeRequest->user
                ->wallets()
                ->where('currency', $ownerGetCurrency)
                ->first();


            $ownerGetWallet->amount += $ownerGetAmount;
            $ownerGetWallet->save();

            $ownerGiveWallet = $exchangeRequest->user
                ->wallets()
                ->where('currency', $ownerGiveCurrency)
                ->first();

            $ownerGiveWallet->amount -= $ownerGiveAmount;
            $ownerGiveWallet->save();

            $applierGetWallet = $user->wallets()
                ->where('currency', $ownerGiveCurrency)
                ->first();

            $applierGetWallet->amount += $ownerGiveAmount;
            $applierGetWallet->save();

            $applierGiveWallet = $user->wallets()
                ->where('currency', $ownerGetCurrency)
                ->first();

            $applierGiveWallet->amount -= $ownerGetAmount + $systemFee;
            $applierGiveWallet->save();

            $exchangeRequest->is_apply = true;
            $exchangeRequest->save();

            ExchangeFee::create([
                'exchange_request_id' => $exchangeRequest->id,
                'currency' => $ownerGetCurrency,
                'fee' => $systemFee,
            ]);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        return true;
    }
}
