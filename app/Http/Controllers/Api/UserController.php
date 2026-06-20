<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $user = auth()->user();
        return $this->successResponse($user, 'Login user data', 200);
    }
}
