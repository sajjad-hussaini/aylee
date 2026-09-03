<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class BannerController extends Controller
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
        $banners = Banner::latest('id')->paginate(10);
        return view('backend.banner.index', compact('banners'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.banner.create');
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
            'title' => 'required|string|max:50',
            'description' => 'nullable|string',
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        $slug = generateUniqueSlug($request->title, Banner::class);
        $validatedData['slug'] = $slug;

        $banner_media = $this->categoryService->storeImage($request)[0] ?? null;

        $banner = Banner::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'photo' => $banner_media,
            'status' => $validatedData['status'],
            'slug' => $validatedData['slug'],
        ]);

        $message = $banner
            ? 'Banner successfully added'
            : 'Error occurred while adding banner';

        return redirect()->route('banner.index')->with(
            $banner ? 'success' : 'error',
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
        $banner = Banner::findOrFail($id);
        return view('backend.banner.edit', compact('banner'));
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
        $banner = Banner::findOrFail($id);

        $validatedData = $request->validate([
            'title' => 'required|string|max:50',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        $banner_media = $request->hasFile('photo')
            ? ($this->categoryService->storeImage($request)[0] ?? null)
            : $banner->photo;

        $status = $banner->update([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'photo' => $banner_media,
            'status' => $validatedData['status'],
        ]);

        $message = $status
            ? 'Banner successfully updated'
            : 'Error occurred while updating banner';

        return redirect()->route('banner.index')->with(
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
        $banner = Banner::findOrFail($id);
        $status = $banner->delete();

        $message = $status
            ? 'Banner successfully deleted'
            : 'Error occurred while deleting banner';

        return redirect()->route('banner.index')->with(
            $status ? 'success' : 'error',
            $message
        );
    }

}
