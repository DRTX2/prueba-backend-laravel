<?php

use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Crear divisa de prueba
    $this->currency = Currency::create([
        'name' => 'Dólar Estadounidense',
        'symbol' => 'USD',
        'exchange_rate' => 1.0,
    ]);

    $this->euroCurrency = Currency::create([
        'name' => 'Euro',
        'symbol' => 'EUR',
        'exchange_rate' => 0.92,
    ]);
});

describe('GET /api/products', function () {
    it('devuelve lista vacía cuando no hay productos', function () {
        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [],
            ]);
    });

    it('devuelve lista de productos paginada', function () {
        Product::factory()->count(5)->create([
            'currency_id' => $this->currency->id,
        ]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'price',
                        'currency',
                        'tax_cost',
                        'manufacturing_cost',
                        'total_cost',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
            ]);

        expect($response->json('meta.total'))->toBe(5);
    });

    it('respeta el parámetro per_page', function () {
        Product::factory()->count(10)->create([
            'currency_id' => $this->currency->id,
        ]);

        $response = $this->getJson('/api/products?per_page=3');

        $response->assertStatus(200);
        expect($response->json('meta.per_page'))->toBe(3);
        expect(count($response->json('data')))->toBe(3);
    });
});

describe('POST /api/products', function () {
    it('crea un producto correctamente', function () {
        $productData = [
            'name' => 'Producto de Prueba',
            'description' => 'Descripción del producto de prueba',
            'price' => 99.99,
            'currency_id' => $this->currency->id,
            'tax_cost' => 15.99,
            'manufacturing_cost' => 30.00,
        ];

        $response = $this->postJson('/api/products', $productData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Producto creado exitosamente.',
            ])
            ->assertJsonPath('data.name', 'Producto de Prueba')
            ->assertJsonPath('data.price', 99.99);

        $this->assertDatabaseHas('products', [
            'name' => 'Producto de Prueba',
            'price' => 99.99,
        ]);
    });

    it('valida campos requeridos', function () {
        $response = $this->postJson('/api/products', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'price', 'currency_id']);
    });

    it('valida que el precio sea positivo', function () {
        $response = $this->postJson('/api/products', [
            'name' => 'Producto',
            'price' => -10,
            'currency_id' => $this->currency->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['price']);
    });

    it('valida que la divisa exista', function () {
        $response = $this->postJson('/api/products', [
            'name' => 'Producto',
            'price' => 100,
            'currency_id' => 9999,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['currency_id']);
    });

    it('crea producto con valores por defecto para tax_cost y manufacturing_cost', function () {
        $response = $this->postJson('/api/products', [
            'name' => 'Producto Simple',
            'price' => 50.00,
            'currency_id' => $this->currency->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.tax_cost', 0)
            ->assertJsonPath('data.manufacturing_cost', 0);
    });
});

describe('GET /api/products/{id}', function () {
    it('devuelve un producto por ID', function () {
        $product = Product::factory()->create([
            'name' => 'Producto Específico',
            'currency_id' => $this->currency->id,
        ]);

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $product->id,
                    'name' => 'Producto Específico',
                ],
            ]);
    });

    it('devuelve 404 para producto no existente', function () {
        $response = $this->getJson('/api/products/9999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Producto no encontrado.',
            ]);
    });
});

describe('PUT /api/products/{id}', function () {
    it('actualiza un producto correctamente', function () {
        $product = Product::factory()->create([
            'currency_id' => $this->currency->id,
        ]);

        $response = $this->putJson("/api/products/{$product->id}", [
            'name' => 'Producto Actualizado',
            'price' => 199.99,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Producto actualizado exitosamente.',
            ])
            ->assertJsonPath('data.name', 'Producto Actualizado')
            ->assertJsonPath('data.price', 199.99);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Producto Actualizado',
        ]);
    });

    it('devuelve 404 al actualizar producto no existente', function () {
        $response = $this->putJson('/api/products/9999', [
            'name' => 'Nombre',
        ]);

        $response->assertStatus(404);
    });

    it('valida datos al actualizar', function () {
        $product = Product::factory()->create([
            'currency_id' => $this->currency->id,
        ]);

        $response = $this->putJson("/api/products/{$product->id}", [
            'price' => -100,
        ]);

        $response->assertStatus(422);
    });
});

describe('DELETE /api/products/{id}', function () {
    it('elimina un producto (soft delete)', function () {
        $product = Product::factory()->create([
            'currency_id' => $this->currency->id,
        ]);

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Producto eliminado exitosamente.',
            ]);

        // Verificar soft delete
        $this->assertSoftDeleted('products', [
            'id' => $product->id,
        ]);
    });

    it('devuelve 404 al eliminar producto no existente', function () {
        $response = $this->deleteJson('/api/products/9999');

        $response->assertStatus(404);
    });
});

describe('GET /api/products/{id}/prices', function () {
    it('devuelve lista de precios del producto', function () {
        $product = Product::factory()->create([
            'currency_id' => $this->currency->id,
        ]);

        ProductPrice::create([
            'product_id' => $product->id,
            'currency_id' => $this->euroCurrency->id,
            'price' => 91.99,
        ]);

        $response = $this->getJson("/api/products/{$product->id}/prices");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(1, 'data');
    });

    it('devuelve 404 para producto no existente', function () {
        $response = $this->getJson('/api/products/9999/prices');

        $response->assertStatus(404);
    });
});

describe('POST /api/products/{id}/prices', function () {
    it('crea un nuevo precio para un producto', function () {
        $product = Product::factory()->create([
            'currency_id' => $this->currency->id,
        ]);

        $response = $this->postJson("/api/products/{$product->id}/prices", [
            'currency_id' => $this->euroCurrency->id,
            'price' => 87.50,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Precio del producto creado exitosamente.',
            ])
            ->assertJsonPath('data.price', 87.5);

        $this->assertDatabaseHas('product_prices', [
            'product_id' => $product->id,
            'currency_id' => $this->euroCurrency->id,
            'price' => 87.50,
        ]);
    });

    it('valida que no exista precio duplicado para misma divisa', function () {
        $product = Product::factory()->create([
            'currency_id' => $this->currency->id,
        ]);

        ProductPrice::create([
            'product_id' => $product->id,
            'currency_id' => $this->euroCurrency->id,
            'price' => 90.00,
        ]);

        $response = $this->postJson("/api/products/{$product->id}/prices", [
            'currency_id' => $this->euroCurrency->id,
            'price' => 95.00,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['currency_id']);
    });

    it('devuelve 404 para producto no existente', function () {
        $response = $this->postJson('/api/products/9999/prices', [
            'currency_id' => $this->euroCurrency->id,
            'price' => 100,
        ]);

        $response->assertStatus(404);
    });
});
