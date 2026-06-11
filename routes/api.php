<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\ProductController as ApiProductController;
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

    // protected routes added here 
    Route::get('/products', [ApiProductController::class, 'index']);
    Route::get('/products/{id}', [ApiProductController::class, 'show']);
    Route::post('/products', [ApiProductController::class, 'store']);
    Route::put('/products/{id}', [ApiProductController::class, 'update']);
    Route::delete('/products/{id}', [ApiProductController::class, 'destroy']);
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});







