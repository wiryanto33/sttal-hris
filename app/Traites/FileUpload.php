<?php

namespace App\Traites;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

trait FileUpload
{

    public function uploadFile(UploadedFile $file, string $directory = 'uploads'): string
    {
        $fileName = 'User_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // move the file to storage

        $file->move(public_path($directory), $fileName);
        return '/' . $directory . '/' . $fileName;
    }

    // public function deleteFile(string $path) : bool {
    //     if(File::exists(public_path($path))){
    //         File::delete(public_path($path));

    //         return true;
    //     }
    //     return false;
    // }

    public function deleteFile(?string $path): bool
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
            return true;
        }
        return false;
    }
}
