<?php

use App\Http\Controllers\etiket\user\dashboard;
use App\Http\Controllers\etiket\in\login;
use App\Http\Controllers\etiket\in\register;
use App\Http\Controllers\homepage\beranda;
use App\Http\Controllers\sampel;
use App\Http\Controllers\homepage\panduan;
use App\Http\Controllers\homepage\booking;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('beranda');
});

Route::get('beranda', [beranda::class, 'index'])->name('homepage.beranda');
Route::get('sop', [panduan::class, 'sop'])->name('sop');
Route::get('panduan', [panduan::class, 'panduan'])->name('panduan');
Route::get('booking', [booking::class, 'index'])->name('booking');

Route::middleware('guest')->group(function () {
    Route::get('login', [login::class, 'login'])->name('etiket.in.login');
    Route::post('login', [login::class, 'actionlogin'])->name('etiket.in.actionlogin');
    Route::post('logout', [login::class, 'logout'])->name('etiket.in.logout');
    Route::get('register', [register::class, 'register'])->name('etiket.in.register');
    Route::post('register', [register::class, 'actionregister'])->name('etiket.in.actionregister');
});


// admin
Route::get('admin/dashboard', [dashboard::class, 'index'])->name('admin.dashboard');



// sampel
Route::get('sampel/{blade}', [sampel::class, 'index'])->name('sampel.index');
