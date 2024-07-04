<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class sampel extends Controller
{
    public function index($blade)
    {
        $laman = 'sampel.' . $blade;

        if (View::exists($laman)) {
            return view($laman);
        } else {
            abort(404);
        }
    }
}
