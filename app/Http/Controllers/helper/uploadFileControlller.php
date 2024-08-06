<?php

namespace App\Http\Controllers\helper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class uploadFileControlller extends Controller
{
    public function create(string $type, string $folder, UploadedFile $file)
    {

        if ($file) {
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = "upload/" . $type . "/" . $folder . "/";
            $file->move(public_path($path), $fileName);
            $fileUrl = $path . $fileName;

            return $fileUrl;
        }
        return null;
    }

    public function delete($path)
    {
        if (file_exists(public_path($path))) {
            unlink(public_path($path));
            return true;
        }
        return false;
    }
}
