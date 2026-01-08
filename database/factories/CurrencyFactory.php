<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

class CurrencyFactory extends Factory
{
    protected $model = Currency::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->currencyCode(),
            'symbol' => $this->faker->randomElement(['$', '€', '£', '¥', '₿', '₽']),
            'exchange_rate' => $this->faker->randomFloat(6, 0.5, 100),
        ];
    }
}
