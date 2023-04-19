<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class WalletsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = User::select('id')->pluck('id')->toArray();
        $walletCurrencies = [User::UAH, User::USD, User::EUR];

        foreach ($userIds as $userId) {
            $randomCurrenciesCount = rand(0, 3);
            if ($randomCurrenciesCount === 0) {
                continue;
            }
            $randomUserWalletCurrencies = Arr::random($walletCurrencies, $randomCurrenciesCount);
            foreach ($randomUserWalletCurrencies as $randomUserWalletCurrency) {
                Wallet::firstOrCreate([
                    'user_id' => $userId,
                    'currency' => $randomUserWalletCurrency,
                    'amount' => rand(1, 2000),
                ]);
            }
        }
    }
}
