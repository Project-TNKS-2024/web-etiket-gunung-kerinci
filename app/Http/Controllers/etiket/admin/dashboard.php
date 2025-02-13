<?php

namespace App\Http\Controllers\etiket\admin;

use App\Http\Controllers\AdminController;


class dashboard extends AdminController
{
    public function index()
    {
        return view('etiket.admin.dashboard');
    }
}
