<?php

namespace App\Http\Controllers\homepage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class booking extends Controller
{
    //

    public function index(Request $request)
    {

        return view("homepage.booking");
    }
}
