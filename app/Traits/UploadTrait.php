<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Image; // This is the facade

trait UploadTrait
{
    public function uploadFile(UploadedFile $uploadedFile, $folder = null, $disk = 'public_uploads', $filename = null)
    {
        $name = !is_null($filename) ? $filename : Str::random(25);

        $file = $uploadedFile->storeAs($folder, $name.'.'.$uploadedFile->getClientOriginalExtension(), $disk);

        return $file;
    }

    public function deleteFile($media_path)
    {
        if (File::exists($media_path)) { // unlink or remove previous image from folder
            unlink($media_path);
        }
    }

    public function findOriginalFile($path)
    {
        return Str::replaceLast('.', '_original.', $path);
    }

    public function findThumbFile($path)
    {
        return Str::replaceLast('.', '_thumbnail.', $path);
    }


    public function optimizeFile($path, $type)
    {
        switch ($type){
            case "image":
                $this->compressImage($path);
                $this->thumbnail($path);
                break;
            case "file":
                break;
            case "video":
                break;
        }
    }

    public function whatIsMyMimeType($file)
    {
        $mimeType = explode('/', $file->getMimeType());

        if ($mimeType[0] == 'video') 
        {
            return 'video';
        }
        elseif($mimeType[0] == 'image')
        {
            return 'image';
        }
        elseif($mimeType[0] == 'text')
        {
            return 'text';
        }
        elseif($mimeType[0] == 'audio')
        {
            return 'audio';
        }
        elseif($mimeType[0] == 'application')
        {
            return 'application';
        }
        else
        {
            return 'unknown';
        }

    }


    function compressImage($path, $keepOriginal = true)
    {
        if ($keepOriginal) {
            $origFilePath = public_path('/') . Str::replaceLast('.', '_original.', $path);
            rename(public_path('/') . $path, $origFilePath);
        } else {
            $origFilePath = public_path('/') . $path;
        }

        $img = Image::make($origFilePath);

            $imgSize = filesize($origFilePath) / 1024;
            if ($imgSize <= 100) {
                $quality = 80;
            } elseif ($imgSize <= 200) {
                $quality = 75;
            } elseif ($imgSize <= 400) {
                $quality = 65;
            } elseif ($imgSize <= 800) {
                $quality = 55;
            } elseif ($imgSize <= 1024) {
                $quality = 45;
            } elseif ($imgSize <= 2048) {
                $quality = 35;
            } elseif ($imgSize <= 4096) {
                $quality = 30;
            } else {
                $quality = 20;
            }

        if ($keepOriginal) {
            return $img->save(public_path('/') . $path, $quality);
        } else {
            return $img->save(null, $quality);
        }
    }


    /*
    * Compress Image for thumbnail
    */

    function thumbnail($path)
    {
        $thumbnailPath = Str::replaceLast('.', '_thumbnail.', $path);
        $fullPath = public_path('/') . $path;

        // For Intervention Image 2.7.2 with facade
        $img = Image::make($fullPath);

        // Width 300px tak resize karo (aspect ratio maintain, chhoti image upscale na ho)
        $img->resize(300, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Resize ke baad quality 70 kaafi hai — chhoti aur saaf dono
        return $img->save(public_path('/') . $thumbnailPath, 70);
    }


}
