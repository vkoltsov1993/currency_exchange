<?php

namespace App\Services\ExchangeRequestService;

use App\Exceptions\UserDoesNotHaveWalletException;
use App\Models\ExchangeRequest;
use App\Models\User;

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
    public function handle(User $user, array $data): ExchangeRequest
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
}
