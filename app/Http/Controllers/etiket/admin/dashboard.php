<?php

namespace App\Http\Controllers\etiket\admin;

use App\Http\Controllers\AdminController;
use App\Models\destinasi;

class dashboard extends AdminController
{
    public function index()
    {
        $listDestinasi = destinasi::with('gambar_destinasi')->get();
        return view('etiket.admin.dashboard', [
            'listDestinasi' => $listDestinasi,
        ]);
    }
}
