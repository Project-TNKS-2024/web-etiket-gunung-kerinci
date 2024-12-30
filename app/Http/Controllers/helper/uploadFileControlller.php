<?php

namespace App\Http\Controllers\helper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class uploadFileControlller extends HelperController
{
    public function create(string $type = "id", string $folder = "pendaki", UploadedFile $file)
    {

        if ($file) {
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = "upload/" . $folder . "/" .  $type . "/";
            $file->move(public_path($path), $fileName);
            $fileUrl = $path . $fileName;

            return $fileUrl;
        }
        return null;
    }
    public function upadate(string $url, UploadedFile $file)
    {
        if ($file && file_exists(public_path($url))) {
            // ambil folder dan type dari url
            $folder = explode("/", $url)[1];
            $type = explode("/", $url)[2];
            unlink(public_path($url));
            $path = $this->create($type, $folder, $file);
            return $path;
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
    public function get($path)
    {
        if (file_exists(public_path($path))) {
            return public_path($path);
        } else {
            return public_path('assets/img/sampel/sampel 2.png');
        }
        return null;
    }
}
