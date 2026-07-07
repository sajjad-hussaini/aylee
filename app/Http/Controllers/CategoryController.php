<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryStoreRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    protected $categoryService;

    public function __construct(public CategoryService $category_service)
    {
        return $this->categoryService = $category_service;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::getAllCategory();
        return view('backend.category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $parent_cats = Category::where('is_parent', 1)->orderBy('title', 'ASC')->get();
        return view('backend.category.create', compact('parent_cats'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryStoreRequest $request)
    {
        $slug = generateUniqueSlug($request->title, Category::class);
        $request['slug'] = $slug;
        $request['is_parent'] = $request->input('is_parent', 0);

        // upload category media
        $category_media = $this->categoryService->storeImage($request);

        $category = Category::create([
            'title'=> $request->title,
            'summary'=> $request->summary,
            'is_parent' => $request->is_parent,
            'parent_id' => $request->parent_id,
            'status' => $request->status,
            'slug' => $request->slug,
            'photo' => $category_media,
            'gender' => $request->gender,
        ]);

        $message = $category
            ? 'Category successfully added'
            : 'Error occurred, Please try again!';

        return redirect()->route('category.index')->with(
            $category ? 'success' : 'error',
            $message
        );
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
        $category = Category::findOrFail($id);
        $parent_cats = Category::where('is_parent', 1)->get();
        return view('backend.category.edit', compact('category', 'parent_cats'));
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
        $category = Category::findOrFail($id);

        $validatedData = $request->validate([
            'title'     => 'required|string',
            'summary'   => 'nullable|string',
            'photo'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'    => 'required|in:active,inactive',
            'is_parent' => 'sometimes|in:1',
            'parent_id' => 'nullable|exists:categories,id',
            'gender'    => 'required|in:male,female',
        ]);

        $validatedData['is_parent'] = $request->input('is_parent', 0);

        // Upload new image if selected
        if ($request->hasFile('photo')) {

            // Optional: delete old image
            $this->categoryService->deleteImage($category->photo);

            $validatedData['photo'] = $this->categoryService->storeImage($request);
        } else {
            // Keep existing image
            $validatedData['photo'] = $category->photo;
        }

        $status = $category->update($validatedData);

        return redirect()->route('category.index')->with(
            $status ? 'success' : 'error',
            $status
                ? 'Category successfully updated.'
                : 'Error occurred, Please try again!'
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
        $category = Category::findOrFail($id);
        $child_cat_id = Category::where('parent_id', $id)->pluck('id');

        $status = $category->delete();

        if ($status && $child_cat_id->count() > 0) {
            Category::shiftChild($child_cat_id);
        }

        $message = $status
            ? 'Category successfully deleted'
            : 'Error while deleting category';

        return redirect()->route('category.index')->with(
            $status ? 'success' : 'error',
            $message
        );
    }

    /**
     * Get child categories by parent ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getChildByParent(Request $request)
    {
        $category = Category::findOrFail($request->id);
        $child_cat = Category::getChildByParentID($request->id);

        if ($child_cat->count() <= 0) {
            return response()->json(['status' => false, 'msg' => '', 'data' => null]);
        }

        return response()->json(['status' => true, 'msg' => '', 'data' => $child_cat]);
    }

    public function getParentCategories(Request $request)
    {
        $parent_cat = Category::getAllParent($request->gender);

        if ($parent_cat->count() <= 0) {
            return response()->json(['status' => false, 'msg' => '', 'data' => null]);
        }

        return response()->json(['status' => true, 'msg' => '', 'data' => $parent_cat]);
    }
}
