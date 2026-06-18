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
}
