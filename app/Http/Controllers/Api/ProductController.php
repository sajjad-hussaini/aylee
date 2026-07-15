<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\FavoriteProduct;
use App\Models\Product;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::query()->with('cat_info', 'sub_cat_info', 'media')
            ->paginate($request->get('per_page', 10));

        return $this->successResponse((new ProductCollection($products)), 'Products retrieved successfully', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::query()->where('id', $id)->first();

        if (!$product) {
            return $this->errorResponse('Product not found', 404);
        }

        return $this->successResponse(['data' => new ProductResource($product)], 'Single Product retrieved successfully', 200);
    }

    public function categoryProducts(Request $request, $categoryId)
    {

        $products = Product::query()
            ->where('cat_id', $categoryId)
            ->with('cat_info', 'sub_cat_info')
            ->paginate($request->get('per_page', 10));

        return $this->successResponse(new ProductCollection($products), 'Products with category retrieved successfully', 200);
    }

    public function focusProducts(Request $request)
    {
        $products = Product::query()
            ->where('section', 'focus')
            ->with('cat_info', 'sub_cat_info', 'media')
            ->paginate($request->get('per_page', 10));

        return $this->successResponse(new ProductCollection($products), 'Focus products retrieved successfully', 200);
    }

    public function mustHaveProducts(Request $request)
    {
        $products = Product::query()
            ->where('section', 'must_haves')
            ->with('cat_info', 'sub_cat_info', 'media')
            ->paginate($request->get('per_page', 10));

        return $this->successResponse(new ProductCollection($products), 'Must-Haves products retrieved successfully', 200);
    }

    public function saleEssentialProducts(Request $request)
    {
        $products = Product::query()
            ->where('section', 'sale_essentials')
            ->with('cat_info', 'sub_cat_info', 'media')
            ->paginate($request->get('per_page', 10));

        return $this->successResponse(new ProductCollection($products), 'Sale essentials products retrieved successfully', 200);
    }

    public function addFavoriteProduct(Request $request)
    {
        $productId = $request->input('product_id');
        $user = $request->user();
        $guestToken = $request->header('X-Guest-Token');

        if (!$productId) {
            return $this->errorResponse('Product ID is missing', 400);
        }

        if (!$user && !$guestToken) {
            return $this->errorResponse('Guest token required', 422);
        }

        $query = FavoriteProduct::where('product_id', $productId);

        $user
            ? $query->where('user_id', $user->id)
            : $query->where('guest_token', $guestToken)->whereNull('user_id');

        if ($query->exists()) {
            return $this->errorResponse('Product is already in favorites', 400);
        }

        FavoriteProduct::create([
            'user_id'     => $user?->id,
            'guest_token' => $user ? null : $guestToken,
            'product_id'  => $productId,
        ]);

        return $this->successResponse(null, 'Product added to favorites successfully', 200);
    }

    public function removeFavoriteProduct(Request $request)
    {
        $user = $request->user();
        $productId = $request->input('product_id');

        if (!$productId) {
            return $this->errorResponse('Product ID is missing', 400);
        }

        if (!self::isProductFavorite($productId, $user)) {
            return $this->errorResponse('Product is not in favorites', 400);
        }

        FavoriteProduct::where('product_id', $productId)
            ->where('user_id', $user->id)
            ->whereOr(function ($query) use ($request) {
                $guestToken = $request->header('X-Guest-Token');
                if ($guestToken) {
                    $query->where('guest_token', $guestToken)->whereNull('user_id');
                }
            })
            ->delete();

        return $this->successResponse(null, 'Product removed from favorites successfully', 200);
    }

    public function favoriteList(Request $request)
    {
        $user = $request->user();
        $guestToken = $request->header('X-Guest-Token');
        $favorites = [];

        if ($user) {
            $favorites = FavoriteProduct::where('user_id', $user->id)->get();
        } elseif ($guestToken) {
            $favorites = FavoriteProduct::where('guest_token', $guestToken)->whereNull('user_id')->get();
        }

        return $this->successResponse(new ProductCollection($favorites), 'Favorite products retrieved successfully', 200);
    }

    public function isProductFavorite($productId, $user = null, $guestToken = null)
    {
        $query = FavoriteProduct::where('product_id', $productId);

        $user
            ? $query->where('user_id', $user->id)
            : $query->where('guest_token', $guestToken)->whereNull('user_id');

        return $query->exists();
    }
}
