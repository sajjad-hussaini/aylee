<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryCollection;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Category;

class CategoryController extends Controller
{
    use ApiResponseTrait;
    
    public function index()
    {
        $categories = Category::query()->paginate(10);
        return $this->successResponse(
            new CategoryCollection($categories),
            'Categories retrieved successfully',
            200
        );
    }
}
