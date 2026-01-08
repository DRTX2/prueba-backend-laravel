<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            [
                'name' => 'Dólar Estadounidense',
                'symbol' => 'USD',
                'exchange_rate' => 1.000000,
            ],
            [
                'name' => 'Euro',
                'symbol' => 'EUR',
                'exchange_rate' => 0.920000,
            ],
            [
                'name' => 'Peso Mexicano',
                'symbol' => 'MXN',
                'exchange_rate' => 17.150000,
            ],
            [
                'name' => 'Peso Colombiano',
                'symbol' => 'COP',
                'exchange_rate' => 3950.000000,
            ],
            [
                'name' => 'Libra Esterlina',
                'symbol' => 'GBP',
                'exchange_rate' => 0.790000,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['symbol' => $currency['symbol']],
                $currency
            );
        }
    }
}
