<?php

namespace App\Http\Controllers\etiket\admin\destinasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class bookingController extends Controller
{
    public function index($id)
    {
        return $id;
        return view('etiket.admin.destinasi.booking.index', compact('id'));
    }
}
