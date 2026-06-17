<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponseTrait;
    
    public function index()
    {
        $categories = Category::all();
        return $this->successResponse($categories, 'Categories retrieved successfully', 200);
    }
}
