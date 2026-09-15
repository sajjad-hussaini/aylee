<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductMedia;
use App\Traits\UploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    use UploadTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::getAllProduct();
        return view('backend.product.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::where('is_parent', 1)->get();
        $brands = Brand::get();
        return view('backend.product.create', compact('categories', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    
        $validatedData = $request->validate([
            'title'         => 'required',
            'summary'       => 'required|string',
            'size'          => 'nullable',
            'colors'        => 'nullable',
            'stock'         => 'required|numeric',
            'description'   => 'nullable|string',
            'cat_id'        => 'required|exists:categories,id',
            'brand_id'      => 'nullable|exists:brands,id',
            'child_cat_id'  => 'nullable|exists:categories,id',
            'section'       => 'nullable|in:common,focus,must_haves,sale_essentials',
            'is_featured'   => 'sometimes|in:1',
            'status'        => 'required|in:active,inactive',
            'price'         => 'required|numeric',
            'discount'      => 'required|numeric',
            'temp_images'   => 'nullable|array',
            'temp_images.*' => 'nullable|string',
            'primary_image' => 'nullable|string',
            'size_chart'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $normalizedVariants = $this->storeVariantImages(
            Product::normalizeVariants($request->input('variants', []))
        );
        $variantSizes = collect($normalizedVariants)->pluck('size')->filter()->unique()->values()->all();
        $variantColors = collect($normalizedVariants)->pluck('color')->filter()->unique()->values()->all();

        $validatedData['slug']        = generateUniqueSlug($request->title, Product::class);
        $validatedData['section']     = $request->input('section', 'common');
        $validatedData['is_featured'] = $request->input('is_featured', 0);
        $validatedData['variants']    = $normalizedVariants;
        $validatedData['size']        = !empty($variantSizes)
                                            ? implode(',', $variantSizes)
                                            : ($request->has('size') ? implode(',', $request->input('size')) : '');
        $validatedData['colors']      = !empty($variantColors) ? $variantColors : ($request->has('colors') ? $request->input('colors') : []);

        $finalImagePaths      = $this->moveTempImages($request->input('temp_images', []));
        $validatedData['photo'] = !empty($finalImagePaths)
                                    ? json_encode($finalImagePaths)
                                    : null;

        $primaryIndex = array_search($request->input('primary_image'), $request->input('temp_images', []), true);
        if ($request->hasFile('size_chart')) {
            $validatedData['size_chart'] = $request->file('size_chart')
                ->store('uploads/size-charts/' . date('Y') . '/' . date('m'), 'public_uploads');
        }

        $product = Product::create($validatedData);

        foreach ($finalImagePaths as $index => $path) {
            ProductMedia::create([
                'product_id' => $product->id,
                'path'       => $path,
                'disk'       => 'public_uploads',
                'is_primary' => $primaryIndex === false ? $index === 0 : $index === $primaryIndex,
                'sort_order' => $index,
            ]);
        }

        $message = $product ? 'Product Successfully added' : 'Please try again!!';

        return redirect()->route('product.index')->with(
            $product ? 'success' : 'error',
            $message
        );
    }

    private function moveTempImages(array $tempPaths): array
    {
        $finalPaths = [];

        if (empty($tempPaths)) {
            return $finalPaths;
        }

        $folder = 'uploads/products/' . date('Y') . '/' . date('m');
        $day    = date('d');
        $time   = md5(time());

        foreach ($tempPaths as $key => $tempPath) {
            // Null ya empty skip karo
            if (is_null($tempPath) || trim($tempPath) === '') {
                continue;
            }

            $extension    = pathinfo($tempPath, PATHINFO_EXTENSION);
            $keyGenerate  = generateKey();
            $fullFileName = $keyGenerate . '_' . date('d') . '_' . $time . '_' . $key;
            $finalPath    = $folder . '/' . $fullFileName . '.' . $extension;

            // Temp se final location pe move
            Storage::disk('public_uploads')->move($tempPath, $finalPath);

            // Image ko compress karo aur thumbnail banao (fast loading ke liye)
            if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $this->optimizeFile($finalPath, 'image');
            }

            $finalPaths[] = $finalPath;
        }

        return $finalPaths;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Implement if needed
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::with('media')->findOrFail($id);
        $categories = Category::where('is_parent', 1)->get();
        $items = Product::where('id', $id)->get();
        $brands = Brand::get();

        return view('backend.product.edit', compact('product', 'brands', 'categories', 'items'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validatedData = $request->validate([
            'title'            => 'required|string',
            'summary'          => 'required|string',
            'description'      => 'nullable|string',
            'size'             => 'nullable',
            'colors'           => 'nullable',
            'stock'            => 'required|numeric',
            'cat_id'           => 'required|exists:categories,id',
            'child_cat_id'     => 'nullable|exists:categories,id',
            'section'          => 'nullable|in:common,focus,must_haves,sale_essentials',
            'is_featured'      => 'sometimes|in:1',
            'brand_id'         => 'nullable|exists:brands,id',
            'status'           => 'required|in:active,inactive',
            'condition'        => 'nullable|in:default,new,hot',
            'price'            => 'required|numeric',
            'discount'         => 'nullable|numeric',
            'temp_images'      => 'nullable|array',
            'temp_images.*'    => 'nullable|string',
            'primary_media_id' => 'nullable|integer|exists:product_media,id',
            'primary_image'    => 'nullable|string',
            'deleted_images'   => 'nullable|array',
            'deleted_images.*' => 'integer|exists:product_media,id',
            'size_chart'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $normalizedVariants = $this->storeVariantImages(
            Product::normalizeVariants($request->input('variants', []))
        );
        $variantSizes = collect($normalizedVariants)->pluck('size')->filter()->unique()->values()->all();
        $variantColors = collect($normalizedVariants)->pluck('color')->filter()->unique()->values()->all();

        $validatedData['section']     = $request->input('section', 'common');
        $validatedData['is_featured'] = $request->input('is_featured', 0);
        $validatedData['variants']    = $normalizedVariants;
        $validatedData['size']        = !empty($variantSizes)
                                        ? implode(',', $variantSizes)
                                        : ($request->has('size') ? implode(',', $request->input('size')) : '');
        $validatedData['colors']      = !empty($variantColors) ? $variantColors : ($request->has('colors') ? $request->input('colors') : []);

        // 1) Pehle deleted images ko handle karo (storage + DB dono se)
        $deletedIds = $request->input('deleted_images', []);
        $mediaChanged = false;

        if (!empty($deletedIds)) {
            $mediaToDelete = $product->media()->whereIn('id', $deletedIds)->get();

            foreach ($mediaToDelete as $media) {
                $disk = $media->disk ?: 'public_uploads';
                if ($media->path && Storage::disk($disk)->exists($media->path)) {
                    Storage::disk($disk)->delete($media->path);
                }
                $media->delete();
            }

            $mediaChanged = true;
        }

        // 2) Naye images (dropzone se) move karo
        $newImagePaths = $this->moveTempImages($request->input('temp_images', []));

        if (!empty($newImagePaths)) {
            $existingCount = $product->media()->count();

            foreach ($newImagePaths as $index => $path) {
                ProductMedia::create([
                    'product_id' => $product->id,
                    'path'       => $path,
                    'disk'       => 'public_uploads',
                    'is_primary' => false,
                    'sort_order' => $existingCount + $index,
                ]);
            }

            $mediaChanged = true;
        }

        $primaryMedia = $request->input('primary_media_id');
        $primaryTempIndex = array_search($request->input('primary_image'), $request->input('temp_images', []), true);

        if ($primaryMedia || $primaryTempIndex !== false) {
            $product->media()->update(['is_primary' => false]);

            if ($primaryMedia && $product->media()->whereKey($primaryMedia)->exists()) {
                $product->media()->whereKey($primaryMedia)->update(['is_primary' => true]);
            } elseif ($primaryTempIndex !== false && isset($newImagePaths[$primaryTempIndex])) {
                $product->media()->where('path', $newImagePaths[$primaryTempIndex])->update(['is_primary' => true]);
            }

            $mediaChanged = true;
        }

        if ($request->hasFile('size_chart')) {
            if ($product->size_chart && Storage::disk('public_uploads')->exists($product->size_chart)) {
                Storage::disk('public_uploads')->delete($product->size_chart);
            }

            $validatedData['size_chart'] = $request->file('size_chart')
                ->store('uploads/size-charts/' . date('Y') . '/' . date('m'), 'public_uploads');
        }

        // 3) Agar media mein koi change (add ya delete) hua ho tu photo column + primary re-sync karo
        if ($mediaChanged) {
            $remainingMedia = $product->media()->orderBy('sort_order')->get();

            // Agar primary image delete ho gayi thi, tu naya primary assign karo
            if ($remainingMedia->isNotEmpty() && $remainingMedia->where('is_primary', true)->count() === 0) {
                $remainingMedia->first()->update(['is_primary' => true]);
            }

            $validatedData['photo'] = json_encode($remainingMedia->pluck('path')->toArray());
        }

        $status = $product->update($validatedData);

        $message = $status
            ? 'Product Successfully updated'
            : 'Please try again!!';

        return redirect()->route('product.index')->with(
            $status ? 'success' : 'error',
            $message
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $status = $product->delete();

        $message = $status
            ? 'Product successfully deleted'
            : 'Error while deleting product';

        return redirect()->route('product.index')->with(
            $status ? 'success' : 'error',
            $message
        );
    }

    public function tempStore(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $path = $request->file('file')->store('temp', 'public_uploads');

        return response()->json([
            'success' => true,
            'temp_path' => $path,
        ]);
    }

    private function storeVariantImages(array $variants): array
    {
        return collect($variants)->map(function (array $variant) {
            $image = $variant['image'] ?? null;

            if ($image && strpos($image, 'temp/') === 0) {
                $paths = $this->moveTempImages([$image]);
                $variant['image'] = $paths[0] ?? null;
            }

            return $variant;
        })->all();
    }

    public function deleteImage($id)
    {
        $media = ProductMedia::findOrFail($id);

        $disk = $media->disk ?: 'public_uploads';

        if ($media->path && Storage::disk($disk)->exists($media->path)) {
            Storage::disk($disk)->delete($media->path);
        }

        $productId  = $media->product_id;
        $wasPrimary = $media->is_primary;

        $media->delete();

        // Agar primary image delete hui thi, naya primary assign karo
        if ($wasPrimary) {
            $newPrimary = ProductMedia::where('product_id', $productId)
                ->orderBy('sort_order')
                ->first();

            if ($newPrimary) {
                $newPrimary->update(['is_primary' => true]);
            }
        }

        // photo column ko baaki bachi media ke saath resync karo
        $paths = ProductMedia::where('product_id', $productId)
            ->orderBy('sort_order')
            ->pluck('path')
            ->toArray();

        Product::where('id', $productId)->update(['photo' => json_encode($paths)]);

        return response()->json(['success' => true]);
    }
}
