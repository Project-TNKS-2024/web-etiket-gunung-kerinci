<?php

use Illuminate\Support\Facades\Route;
//user
use App\Http\Controllers\etiket\user\dashboard as dasUser;
use App\Http\Controllers\etiket\user\profile as profileUser;
use App\Http\Controllers\etiket\user\gantipassword as resetPasswordUser;
use App\Http\Controllers\etiket\user\riwayat as riwayatUser;


// User routes
Route::middleware(['check.role:user'])->group(function () {

   Route::get('dashboard', [dasUser::class, 'index'])->name('user.dashboard');
   Route::get('dashboard/profile', [profileUser::class, 'index'])->name('user.dashboard.profile');
   Route::post('dashboard/profile/', [profileUser::class, 'action'])->name('user.dashboard.action');
   Route::get('dashboard/riwayat-booking', [riwayatUser::class, 'index'])->name('user.dashboard.riwayat');

   //reset password
   Route::get('dashboard/ganti-password', [resetPasswordUser::class, 'index'])->name('user.dashboard.reset-password');
   Route::post('dashboard/reset-password', [resetPasswordUser::class, 'resetAction'])->name('user.dashboard.reset-password-action');
});
