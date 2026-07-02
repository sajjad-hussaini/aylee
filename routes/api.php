<?php

use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController as ApiProductController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // user routes added here
    Route::get('login/user', [UserController::class, 'index']);
    Route::get('users/{id}', [UserController::class, 'show']);
});
    // categories routes added here
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('fetch/{category}/category', [CategoryController::class, 'show']);

    // protected routes added here 
    Route::get('/products', [ApiProductController::class, 'index']);
    Route::get('/products/{id}', [ApiProductController::class, 'show']);
    Route::get('category/product/{category_id}', [ApiProductController::class,'categoryProducts']);
    Route::get('fetch/favorites', [ApiProductController::class, 'favoriteList']);
    Route::post('/favorites', [ApiProductController::class, 'addFavoriteProduct']);
    Route::post('delete/favorites', [ApiProductController::class, 'removeFavoriteProduct']);

    // cart routes added here 
    Route::get('fetch/cart/items', [CartController::class, 'index']);
    Route::post('add/to/cart', [CartController::class, 'addToCart']);
    Route::post('quantity/update/{cart}', [CartController::class, 'updateQuantity']);
    Route::delete('remove/cart/item/{cart}', [CartController::class, 'removeFromCart']);
    Route::delete('destory/cart', [CartController::class, 'clearCart']);

    // checkout route added here
    Route::post('/checkout/apply-promo', [CheckoutController::class, 'applyPromoCode']);
    Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder']);

    // order route can be added here
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);
    Route::post('/orders/{order}/reorder', [OrderController::class, 'reorder']);

    // banner route can be added here
    Route::get('/banners', [BannerController::class, 'index']);
    Route::get('/banners/active', [BannerController::class, 'banners']);
    Route::get('/banners/{id}', [BannerController::class, 'specificBanner']);



Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});







