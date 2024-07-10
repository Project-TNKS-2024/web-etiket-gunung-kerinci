<?php

namespace App\Http\Controllers\etiket\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class gantipassword extends Controller
{
    public function index()
    {
        return view('etiket.user.sections.ganti-password');
    }
}
