<?php

namespace App\Http\Controllers\etiket\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class dashboard extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('etiket.user.sections.profile', [
            'user' => $user,
        ]);
    }

}
