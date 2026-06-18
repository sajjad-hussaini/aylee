<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Category;
use App\Services\CategoryService;

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
        return $this->successResponse(new CategoryResource($category), 'Category retrieved successfully', 200);
    }
}
