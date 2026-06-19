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
                compressImage($path);
                thumbnail($path);
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

}
