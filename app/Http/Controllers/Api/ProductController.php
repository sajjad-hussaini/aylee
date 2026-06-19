<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::query()->with('cat_info', 'sub_cat_info')
            ->paginate($request->get('per_page', 10));

        return $this->successResponse((new ProductCollection($products)), 'Products retrieved successfully', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::query()->findOrFail($id);

        return $this->successResponse(new ProductResource($product), 'Single Product retrieved successfully', 200);
    }

    public function categoryProducts(Request $request, $categoryId)
    {

        $products = Product::query()
            ->where('cat_id', $categoryId)
            ->with('cat_info', 'sub_cat_info')
            ->paginate($request->get('per_page', 10));

        return $this->successResponse(new ProductCollection($products), 'Products with category retrieved successfully', 200);
    }

    public function addFavoriteProduct(Request $request)
    {

        $user = $request->user();
        $productId = $request->input('product_id');

        if (!$user || !$productId) {
            return $this->errorResponse('User or Product ID is missing', 400);
        }

        if ($user->isFavorite(intval($productId))) {
            return $this->errorResponse('Product is already in favorites', 400);
        }

        $user->favorites()->attach($productId);

        return $this->successResponse(null, 'Product added to favorites successfully', 200);
    }

    public function removeFavoriteProduct(Request $request)
    {
        $user = $request->user();
        $productId = $request->input('product_id');

        if (!$user || !$productId) {
            return $this->errorResponse('User or Product ID is missing', 400);
        }

        if (!$user->isFavorite(intval($productId))) {
            return $this->errorResponse('Product is not in favorites', 400);
        }

        $user->favorites()->detach($productId);

        return $this->successResponse(null, 'Product removed from favorites successfully', 200);
    }

    public function favoriteList(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return $this->errorResponse('User is not authenticated', 401);
        }

        $favorites = $user->favorites()->with('cat_info', 'sub_cat_info')->paginate($request->get('per_page', 10));

        return $this->successResponse(new ProductCollection($favorites), 'Favorite products retrieved successfully', 200);
    }
}
