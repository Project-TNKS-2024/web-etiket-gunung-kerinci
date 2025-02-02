<?php

use Illuminate\Support\Facades\Route;

//auth
use App\Http\Controllers\etiket\auth\login;
use App\Http\Controllers\etiket\auth\register;
use App\Http\Controllers\etiket\auth\lupapassword;
use App\Http\Controllers\etiket\auth\resetpassword;

Route::middleware('guest')->group(function () {
   // login
   Route::get('login', [login::class, 'login'])->name('login');
   Route::post('login', [login::class, 'actionlogin'])->name('login.action');

   // register
   Route::get('register', [register::class, 'register'])->name('register');
   Route::post('register', [register::class, 'actionregister'])->name('register.action');

   // lupa password
   Route::get('lupaPassword', [lupapassword::class, 'sentEmail'])->name('lupaPassword');
   Route::post('lupaPassword', [lupapassword::class, 'confirmEmail'])->name('lupaPassword.action');

   // Reset Password
   route::get('resetPassword/{token}', [resetpassword::class, 'resetpassword'])->name('resetpassword');
   route::post('resetPassword', [resetpassword::class, 'actionresetpassword'])->name('resetpassword.action');
});

// Logout hanya bisa diakses jika user sudah login
Route::middleware('auth')->group(function () {
   Route::post('logout', [login::class, 'logout'])->name('etiket.auth.logout');
});
