<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductCollection;
use App\Models\Category;
use App\Models\Product;
use App\Services\CategoryService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    public function __construct(protected CategoryService $categoryService)
    {
        
    }
    
    public function index()
    {
        $categories = $this->categoryService->getCategories();
        return $this->successResponse(
            new CategoryCollection($categories),
            'Categories retrieved successfully',
            200
        );
    }

    public function show(Category $category)
    {
        return $this->successResponse(['data' => new CategoryResource($category)], 'Single Category retrieved successfully', 200);
    }

    public function products(Request $request, Category $category)
    {
        $parentCategory = $category->is_parent ? $category : $category->parent_info;

        if (!$parentCategory) {
            return $this->errorResponse('Parent category not found', 404);
        }

        $childCategories = Category::where('parent_id', $parentCategory->id)
            ->where('status', 'active')
            ->get();

        $categoryIds = $childCategories->pluck('id')->toArray();
        $categoryIds[] = $parentCategory->id;

        $products = Product::query()
            ->where(function ($query) use ($categoryIds) {
                $query->whereIn('cat_id', $categoryIds)
                      ->orWhereIn('child_cat_id', $categoryIds);
            })
            ->with('cat_info', 'sub_cat_info', 'media')
            ->paginate($request->get('per_page', 10));

        return $this->successResponse([
            'parent_category' => new CategoryResource($parentCategory),
            'child_categories' => CategoryResource::collection($childCategories),
            'products' => new ProductCollection($products),
        ], 'Category and product data retrieved successfully', 200);
    }
}
