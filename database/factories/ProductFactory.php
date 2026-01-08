<?php

namespace Database\Factories;

use App\Models\Currency;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $price = $this->faker->randomFloat(2, 10, 5000);

        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(3),
            'price' => $price,
            'currency_id' => Currency::factory(),
            'tax_cost' => round($price * 0.16, 2), // IVA 16%
            'manufacturing_cost' => round($price * 0.3, 2), // 30% del precio
        ];
    }
}
