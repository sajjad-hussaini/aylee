<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Traits\UploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        $brands = Brand::get();
        $categories = Category::where('is_parent', 1)->get();
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
            'stock'         => 'required|numeric',
            'cat_id'        => 'required|exists:categories,id',
            'brand_id'      => 'nullable|exists:brands,id',
            'child_cat_id'  => 'nullable|exists:categories,id',
            'is_featured'   => 'sometimes|in:1',
            'status'        => 'required|in:active,inactive',
            'price'         => 'required|numeric',
            'discount'      => 'nullable|numeric',
            'temp_images'   => 'nullable|array',
            'temp_images.*' => 'nullable|string',
        ]);

        $validatedData['slug']        = generateUniqueSlug($request->title, Product::class);
        $validatedData['is_featured'] = $request->input('is_featured', 0);
        $validatedData['size']        = $request->has('size')
                                            ? implode(',', $request->input('size'))
                                            : '';

        // Temp images move karo aur JSON mein save karo
        $finalImagePaths      = $this->moveTempImages($request->input('temp_images', []));
        $validatedData['photo'] = !empty($finalImagePaths)
                                    ? json_encode($finalImagePaths)
                                    : null;

        $product = Product::create($validatedData);

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
        $brands = Brand::get();
        $product = Product::findOrFail($id);
        $categories = Category::where('is_parent', 1)->get();
        $items = Product::where('id', $id)->get();

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
            'title' => 'required|string',
            'summary' => 'required|string',
            'description' => 'nullable|string',
            'photo' => 'required|string',
            'size' => 'nullable',
            'stock' => 'required|numeric',
            'cat_id' => 'required|exists:categories,id',
            'child_cat_id' => 'nullable|exists:categories,id',
            'is_featured' => 'sometimes|in:1',
            'brand_id' => 'nullable|exists:brands,id',
            'status' => 'required|in:active,inactive',
            'condition' => 'required|in:default,new,hot',
            'price' => 'required|numeric',
            'discount' => 'nullable|numeric',
        ]);

        $validatedData['is_featured'] = $request->input('is_featured', 0);

        if ($request->has('size')) {
            $validatedData['size'] = implode(',', $request->input('size'));
        } else {
            $validatedData['size'] = '';
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
}
