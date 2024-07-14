<?php

namespace App\Http\Controllers\homepage;

use App\Http\Controllers\Controller;
use App\Models\destinasi;
use Illuminate\Http\Request;

class beranda extends Controller
{
    public function index()
    {
        $destinasi = destinasi::all();
        // return $destinasi;
        return view('homepage.beranda', compact('destinasi'));
    }
}


