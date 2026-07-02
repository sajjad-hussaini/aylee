<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BannerResource;
use App\Models\Banner;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    use ApiResponseTrait;
    public function index()
    {
        $banners = Banner::latest('id')->paginate(10);
        return $this->successResponse(BannerResource::collection($banners), 'Banners retrieved successfully', 200);
    }

    public function banners(Request $request)
    {
        $banners = Banner::where('status', 'active')->latest('id')->get();
        return $this->successResponse(BannerResource::collection($banners),'Active banners retrieved successfully', 200);
    }

    public function specificBanner(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);
        return $this->successResponse(new BannerResource($banner), 'Specific banner retrieved successfully', 200);
    }
}
