<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener la divisa USD como base
        $usd = Currency::where('symbol', 'USD')->first();

        if (!$usd) {
            $this->command->error('Primero debes ejecutar CurrencySeeder');
            return;
        }

        $products = [
            [
                'name' => 'Laptop HP ProBook 450',
                'description' => 'Laptop profesional con procesador Intel Core i7, 16GB RAM, 512GB SSD. Ideal para trabajo empresarial y desarrollo de software.',
                'price' => 899.99,
                'tax_cost' => 143.99,
                'manufacturing_cost' => 450.00,
            ],
            [
                'name' => 'Monitor Dell UltraSharp 27"',
                'description' => 'Monitor 4K IPS con calibración de colores profesional, puertos USB-C y tecnología anti-reflejos.',
                'price' => 549.99,
                'tax_cost' => 87.99,
                'manufacturing_cost' => 220.00,
            ],
            [
                'name' => 'Teclado Mecánico Logitech MX Keys',
                'description' => 'Teclado inalámbrico con retroiluminación inteligente y teclas de perfil bajo. Compatible con múltiples dispositivos.',
                'price' => 119.99,
                'tax_cost' => 19.19,
                'manufacturing_cost' => 35.00,
            ],
            [
                'name' => 'Mouse Ergonómico MX Master 3',
                'description' => 'Mouse inalámbrico con diseño ergonómico, scroll electromagnético y sensor de 4000 DPI.',
                'price' => 99.99,
                'tax_cost' => 15.99,
                'manufacturing_cost' => 28.00,
            ],
            [
                'name' => 'Auriculares Sony WH-1000XM5',
                'description' => 'Auriculares con cancelación de ruido líder en la industria, 30 horas de batería y audio Hi-Res.',
                'price' => 349.99,
                'tax_cost' => 55.99,
                'manufacturing_cost' => 120.00,
            ],
        ];

        // Obtener todas las divisas para crear precios
        $currencies = Currency::where('symbol', '!=', 'USD')->get();

        foreach ($products as $productData) {
            $product = Product::create([
                'name' => $productData['name'],
                'description' => $productData['description'],
                'price' => $productData['price'],
                'currency_id' => $usd->id,
                'tax_cost' => $productData['tax_cost'],
                'manufacturing_cost' => $productData['manufacturing_cost'],
            ]);

            // Crear precios en otras divisas
            foreach ($currencies as $currency) {
                ProductPrice::create([
                    'product_id' => $product->id,
                    'currency_id' => $currency->id,
                    'price' => round($productData['price'] * $currency->exchange_rate, 2),
                ]);
            }
        }
    }
}
