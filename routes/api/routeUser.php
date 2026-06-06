<?php

use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\DestinasiController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // beranda
    Route::get('/beranda', [HomeController::class, 'beranda'])->name('api.beranda');
    // api profile
    Route::get('/profile/pendaki-identity', [ProfileController::class, 'getPendakiIdentity'])->name('api.profile.pendaki-identity');
    Route::get('/profile/getbiodata', [ProfileController::class, 'getBiodata'])->name('api.profile.getbiodata');
    Route::post('/profile/updatebiodata', [ProfileController::class, 'updateBiodata'])->name('api.profile.updatebiodata');
    Route::post('/profile/gantipassword', [ProfileController::class, 'gantiPassword'])->name('api.profile.gantipassword');

    // api destinasi
    Route::get('/destinasi', [DestinasiController::class, 'index'])->name('api.destinasi.index');
    Route::get('/destinasi/{id}', [DestinasiController::class, 'show'])->name('api.destinasi.show');


    // api mytiket

});

// api data  lain
