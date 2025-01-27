<?php

namespace App\Http\Controllers\homepage;

use App\Http\Controllers\Controller;
use App\Models\destinasi;
use Illuminate\Http\Request;

class HomepageController extends Controller
{
    public function Beranda()
    {
        $destinasi = destinasi::all();
        // return $destinasi;
        return view('homepage.beranda', compact('destinasi'));
    }
    public function sop()
    {
        return view('homepage.sop-written');
    }

    public function panduan()
    {
        return view('homepage.panduan');
    }
}
