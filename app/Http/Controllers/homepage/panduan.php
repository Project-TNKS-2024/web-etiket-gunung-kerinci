<?php

namespace App\Http\Controllers\homepage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class panduan extends Controller
{
    //

    public function sop()
    {
        return view('homepage.sop-written');
    }

    public function panduan()
    {
        return view('homepage.panduan');
    }
}
