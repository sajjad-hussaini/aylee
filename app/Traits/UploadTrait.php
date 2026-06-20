<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

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

   

    /*
    * Compress Image
    */
    function compressImage($path, $keepOriginal = true)
    {
        if ($keepOriginal) {
            $origFilePath = public_path('/') . Str::replaceLast('.', '_original.', $path);
            rename(public_path('/') . $path, $origFilePath);
        } else {
            $origFilePath = $path;
        }
        $img = \Image::make($origFilePath);
        $img->orientate();
        $imgSize = $img->filesize() / 1024;
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
            return $img->save($path, $quality, 'jpg');
        } else {
            return $img->save(null, $quality, 'jpg');
        }
    }



    /*
    * Compress Image for thumbnail
    */
    function thumbnail($path)
    {
        $thumbnailPath = Str::replaceLast('.', '_thumbnail.', $path);
        $img = \Image::make(public_path('/') . $path);
        $img->orientate();
        $imgSize = $img->filesize() / 1024;
        if ($imgSize <= 100) {
            $quality = 10; //60
        } elseif ($imgSize <= 200) {
            $quality = 10; //55
        } elseif ($imgSize <= 400) {
            $quality = 10; //45
        } elseif ($imgSize <= 800) {
            $quality = 10; //17
        } elseif ($imgSize <= 1024) {
            $quality = 10; //14
        } elseif ($imgSize <= 2048) {
            $quality = 8;
        } elseif ($imgSize <= 4096) {
            $quality = 6;
        } else {
            $quality = 4;
        }
        return $img->save($thumbnailPath, $quality, 'jpg');
    }


}
