<?php

namespace App\Http\Controllers\etiket\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class dashboard extends Controller
{
    public function index()
    {
        return view('etiket.user.sections.profile');
    }
}
