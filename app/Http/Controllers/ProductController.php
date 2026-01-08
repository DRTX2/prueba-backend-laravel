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

/**
 * @OA\PathItem(path="/api")
 */
class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    /**
     * @OA\Get(
     *     path="/products",
     *     tags={"Productos"},
     *     summary="Listar productos",
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=15)),
     *     @OA\Response(response=200, description="Lista de productos")
     * )
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
     * @OA\Post(
     *     path="/products",
     *     tags={"Productos"},
     *     summary="Crear producto",
     *     @OA\RequestBody(required=true, @OA\JsonContent(type="object")),
     *     @OA\Response(response=201, description="Producto creado"),
     *     @OA\Response(response=422, description="Error de validación")
     * )
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
     * @OA\Get(
     *     path="/products/{id}",
     *     tags={"Productos"},
     *     summary="Obtener producto",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Detalles del producto"),
     *     @OA\Response(response=404, description="No encontrado")
     * )
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
     * @OA\Put(
     *     path="/products/{id}",
     *     tags={"Productos"},
     *     summary="Actualizar producto",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(type="object")),
     *     @OA\Response(response=200, description="Producto actualizado"),
     *     @OA\Response(response=404, description="No encontrado")
     * )
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
     * @OA\Delete(
     *     path="/products/{id}",
     *     tags={"Productos"},
     *     summary="Eliminar producto",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Producto eliminado"),
     *     @OA\Response(response=404, description="No encontrado")
     * )
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
     * @OA\Get(
     *     path="/products/{id}/prices",
     *     tags={"Precios"},
     *     summary="Listar precios del producto",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Lista de precios")
     * )
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
     * @OA\Post(
     *     path="/products/{id}/prices",
     *     tags={"Precios"},
     *     summary="Crear precio",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(type="object")),
     *     @OA\Response(response=201, description="Precio creado"),
     *     @OA\Response(response=422, description="Error de validación")
     * )
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
