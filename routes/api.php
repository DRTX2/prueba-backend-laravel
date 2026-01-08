<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí se registran las rutas de la API. Estas rutas son cargadas por el
| RouteServiceProvider y se asignan al grupo de middleware "api".
|
*/

// Rutas de productos
Route::prefix('products')->group(function () {
    // Obtener lista de productos
    Route::get('/', [ProductController::class, 'index']);

    // Crear un nuevo producto
    Route::post('/', [ProductController::class, 'store']);

    // Obtener un producto por ID
    Route::get('/{product}', [ProductController::class, 'show'])
        ->whereNumber('product');

    // Actualizar un producto
    Route::put('/{product}', [ProductController::class, 'update'])
        ->whereNumber('product');

    // Eliminar un producto
    Route::delete('/{product}', [ProductController::class, 'destroy'])
        ->whereNumber('product');

    // Obtener lista de precios de un producto
    Route::get('/{product}/prices', [ProductController::class, 'getPrices'])
        ->whereNumber('product');

    // Crear un nuevo precio para un producto
    Route::post('/{product}/prices', [ProductController::class, 'storePrice'])
        ->whereNumber('product');
});
