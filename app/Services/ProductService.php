<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    /**
     * Obtener lista paginada de productos.
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Product::with(['currency', 'prices.currency'])
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    /**
     * Crear un nuevo producto.
     */
    public function create(array $data): Product
    {
        $data['tax_cost'] = $data['tax_cost'] ?? 0;
        $data['manufacturing_cost'] = $data['manufacturing_cost'] ?? 0;

        $product = Product::create($data);
        $product->load('currency');

        return $product;
    }

    /**
     * Buscar producto por ID.
     */
    public function findById(int $id): ?Product
    {
        return Product::with(['currency', 'prices.currency'])->find($id);
    }

    /**
     * Actualizar un producto.
     */
    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        $product->load(['currency', 'prices.currency']);

        return $product;
    }

    /**
     * Eliminar un producto (soft delete).
     */
    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    /**
     * Obtener precios de un producto.
     */
    public function getPrices(Product $product): Collection
    {
        return $product->prices()->with('currency')->get();
    }

    /**
     * Crear precio para un producto.
     */
    public function createPrice(Product $product, array $data): ProductPrice
    {
        $data['product_id'] = $product->id;

        $price = ProductPrice::create($data);
        $price->load('currency');

        return $price;
    }
}
