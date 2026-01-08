<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\StoreProductPriceRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductPriceResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    /**
     * Obtener lista de productos.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->query('per_page', 15), 1), 100);
        $products = $this->productService->getAllPaginated($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Lista de productos obtenida exitosamente.',
            'data' => ProductResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    /**
     * Crear un nuevo producto.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Producto creado exitosamente.',
            'data' => new ProductResource($product),
        ], 201);
    }

    /**
     * Obtener un producto por ID.
     */
    public function show(int $id): JsonResponse
    {
        $product = $this->productService->findById($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Producto obtenido exitosamente.',
            'data' => new ProductResource($product),
        ]);
    }

    /**
     * Actualizar un producto.
     */
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $product = $this->productService->findById($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado.',
            ], 404);
        }

        $product = $this->productService->update($product, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Producto actualizado exitosamente.',
            'data' => new ProductResource($product),
        ]);
    }

    /**
     * Eliminar un producto.
     */
    public function destroy(int $id): JsonResponse
    {
        $product = $this->productService->findById($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado.',
            ], 404);
        }

        $this->productService->delete($product);

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado exitosamente.',
        ]);
    }

    /**
     * Obtener lista de precios de un producto.
     */
    public function getPrices(int $productId): JsonResponse
    {
        $product = $this->productService->findById($productId);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado.',
            ], 404);
        }

        $prices = $this->productService->getPrices($product);

        return response()->json([
            'success' => true,
            'message' => 'Precios del producto obtenidos exitosamente.',
            'data' => ProductPriceResource::collection($prices),
        ]);
    }

    /**
     * Crear un nuevo precio para un producto.
     */
    public function storePrice(StoreProductPriceRequest $request, int $productId): JsonResponse
    {
        $product = $this->productService->findById($productId);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado.',
            ], 404);
        }

        $price = $this->productService->createPrice($product, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Precio del producto creado exitosamente.',
            'data' => new ProductPriceResource($price),
        ], 201);
    }
}
