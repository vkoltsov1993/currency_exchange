<?php

namespace App\Services\ExchangeRequestService;

use App\Models\ExchangeFee;
use App\Models\ExchangeRequest;
use App\Models\User;
use App\Models\Wallet;
use App\Repositories\ExchangeRequestRepository;
use Illuminate\Support\Facades\DB;

//class ExchangeRequestHandler implements ExchangeRequestService
//{
//    private User $user;
//    private array $data;
//
//    public function handle(User $user, array $data): bool
//    {
//        $this->user = $user;
//        $this->data = $data;
//        return $this->createRequest();
//    }
//
//    private function isUserHasWallet(): bool
//    {
//        $userCurrenciesRequest = [
//            $this->data['currency_give'],
//            $this->data['currency_get'],
//        ];
//        return $this->user->wallets()->whereIn('currency', $userCurrenciesRequest)->count() === 2;
//    }
//
//    private function createRequest(): bool
//    {
//        if (! $this->isUserHasWallet()) {
//            return false;
//        }
//        $wallet = $this->user->wallets()->where('currency', $this->data['currency_give'])->first();
//
//        if (! $this->isWalletHaveEnoughMoney($wallet, $this->data['amount_give'])) {
//            return false;
//        }
//
//        $rate = 0;
//        if ($this->data['amount_give'] > $this->data['amount_get']) {
//            $rate = $this->data['amount_give'] / $this->data['amount_get'];
//        } elseif ($this->data['amount_give'] < $this->data['amount_get']) {
//            $rate = $this->data['amount_get'] / $this->data['amount_give'];
//        } else {
//            $rate = 1;
//        }
//        $exchangeRequests = (new ExchangeRequestRepository)
//            ->getAvailableRequestsByCurrencies($this->data['currency_get'], $this->data['currency_give']);
//
//
//        DB::beginTransaction();
//        if ($exchangeRequests->isNotEmpty()) {
//            $totalAvailable = $exchangeRequests->sum('amount_give');
//            $totalGetRequest = $this->data['amount_get'];
//            $totalGet = 0;
//            foreach ($exchangeRequests as $exchangeRequest) {
//
//                $availableGet = (int)$exchangeRequest->amount_give;
//                if ($totalAvailable > $totalGetRequest){
//                    $exchangeRequest->amount_give = $availableGet < $totalGetRequest
//                }
//
//
//
//
//
//
//
//
//
//                $exchangeRequest->save();
//
////                ExchangeFee::create([
////                    'exchange_request_id' => $exchangeRequest->id,
////                    'fee' => $this->data['amount_get'] * 0.02,
////                    'currency' => $this->data['currency_get'],
////                ]);
//            }
//            dd($totalAvailable - $totalGet);
//
//            $walletAdd = Wallet::query()->where('user_id', $exchangeRequest->user_id)
//                ->where('currency', $this->data['currency_give'])
//                ->first();
//            $walletSub = Wallet::query()->where('user_id', $exchangeRequest->user_id)
//                ->where('currency', $this->data['currency_get'])
//                ->first();
//
//            $walletAdd->amount += ($this->data['amount_give'] - $totalGet);
//            $walletAdd->save();
//            $walletSub->amount -= ($this->data['amount_give'] - $totalGet);
//            $walletSub->save();
//
//            $walletAdd = $this->user->wallets()
//                ->where('currency', $this->data['currency_get'])
//                ->first();
//            $walletSub = $this->user->wallets()
//                ->where('currency', $this->data['currency_give'])
//                ->first();
//
//            $walletAdd->amount += $this->data['amount_get'];
//            $walletAdd->save();
//            $walletSub->amount -= $this->data['amount_give'];
//            $walletSub->save();
//        } else {
//            $exchangeRequest = ExchangeRequest::create([
//                'user_id' => $this->user->id,
//                'currency_give' => $this->data['currency_give'],
//                'amount_give' => $this->data['amount_give'],
//                'currency_get' => $this->data['currency_get'],
//                'amount_get' => $this->data['amount_get'],
//                'rate' => $rate,
//            ]);
//        }
//        DB::commit();
//
//        return true;
//    }
//
//    private function isWalletHaveEnoughMoney(Wallet $wallet, float $amount): bool
//    {
//        return $wallet->amount >= ($amount + $wallet->user->exchangeRequests()->sum('amount_give'));
//    }
//}
