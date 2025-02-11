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

    public function panduan()
    {
        return view('homepage.panduan');
    }
    public function faq()
    {
        return view('homepage.faq');
    }
    public function snk()
    {
        return view('homepage.snk');
    }
}
