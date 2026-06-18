<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController as ApiProductController;
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

    // categories routes added here
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('fetch/{category}/category', [CategoryController::class, 'show']);

    // protected routes added here 
    Route::get('/products', [ApiProductController::class, 'index']);
    Route::get('/products/{id}', [ApiProductController::class, 'show']);
    Route::get('category/product/{category_id}', [ApiProductController::class,'categoryProducts']);
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});







