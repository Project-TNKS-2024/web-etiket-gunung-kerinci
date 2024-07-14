<?php

namespace App\Http\Controllers\homepage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\gk_gates;

class booking extends Controller
{
    //

    public function index(Request $request)
    {
        $gates = gk_gates::all();
        // return $gates;
        return view("homepage.booking.booking", ["gates" => $gates]);
    }
}
