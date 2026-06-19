<?php

namespace App\Services;

use App\Models\Category;
use App\Traits\UploadTrait;

class CategoryService
{
    use UploadTrait;

    public function getCategories()
    {
        return Category::query()->paginate(10);
    }

     public function storeImage($request)
    {
        $imagesPaths = [];
        if ($request->has('photo') && !is_null($request->photo)) {

            if(is_array($request->photo))
            {
                $images = $request->photo;
            }
            else
            {
                $images_arr = [];
                $images_arr[] = $request->photo;
                $images = $images_arr;
            }
            
            $day = date('d');
            $time = md5(time());

            foreach ($images as $key => $image) {
                //check mime type is video or image
                $type = $this->whatIsMyMimeType($image);
                // create random file names
                $keyGenerate = generateKey();
                // Define folder path
                $folder = 'uploads/category/' . date('Y') . '/' . date('m');
                //Define file name
                $fullFileName = $keyGenerate . '_' . $day . '_' . $time . '_' . $type;
                $path = $folder . '/' . $fullFileName . '.' . $image->getClientOriginalExtension();
                
                // Make a file path where image will be stored [ folder path + file name + file extension]
                $this->uploadFile($image, $folder, 'public_uploads', $fullFileName);
                $this->optimizeFile($path, $type);
                $imagesPaths[] = $path;
                
            }

            return $imagesPaths;
        }
        return null;
    }

}