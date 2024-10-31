<?php

namespace App\Http\Controllers\etiket\admin\master;

use App\Http\Controllers\Controller;
use App\Models\destinasi as ModelDestinasi;
use Illuminate\Http\Request;

class destinasisController extends Controller
{
    public function index()
    {
        $destinasi = ModelDestinasi::all();
        return $destinasi;
        return view('etiket.admin.master.destinasi.index', [
            $destinasi
        ]);
    }
}
