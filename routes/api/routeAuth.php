<?php

use App\Http\Controllers\API\AuthCoontroller;
use Illuminate\Support\Facades\Route;

// Auth
Route::post('/register', [AuthCoontroller::class, 'register'])->name('api.auth.register');
Route::post('/login', [AuthCoontroller::class, 'login'])->name('api.auth.login');
Route::post('/forgot-password', [AuthCoontroller::class, 'sendResetLink'])->name('api.auth.password.forgot');
Route::post('/reset-password', [AuthCoontroller::class, 'reset'])->name('api.auth.password.reset');

// Email Verification
Route::get('/email/verify/{id}/{hash}', [AuthCoontroller::class, 'verify'])
   ->middleware(['signed', 'throttle:6,1'])
   ->name('api.auth.email.verify');

Route::middleware('auth:sanctum')->group(function () {
   Route::get('/email/verify/notice', [AuthCoontroller::class, 'notice'])->name('api.auth.email.notice');
   Route::post('/email/resend', [AuthCoontroller::class, 'resend'])->name('api.auth.email.resend');

   Route::post('/logout', [AuthCoontroller::class, 'logout'])->name('api.auth.logout');
});

// OAuth Google
Route::get('/auth/google/redirect', [AuthCoontroller::class, 'redirectToGoogle'])->name('api.auth.google.redirect');
Route::get('/auth/google/callback', [AuthCoontroller::class, 'handleGoogleCallback'])->name('api.auth.google.callback');
Route::post('/auth/google/token', [AuthCoontroller::class, 'loginWithGoogleToken'])->name('api.auth.google.token');
