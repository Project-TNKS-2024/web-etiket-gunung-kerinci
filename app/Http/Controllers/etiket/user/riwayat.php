<?php

namespace App\Http\Controllers\etiket\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class riwayat extends Controller
{
    public function index()
    {
        return view('etiket.user.sections.riwayat');
    }
}
