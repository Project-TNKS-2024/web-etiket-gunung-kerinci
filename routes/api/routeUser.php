<?php

use App\Http\Controllers\API\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
   // api profile
   Route::get('/profile/getbiodata', [ProfileController::class, 'getBiodata'])->name('api.profile.getbiodata');
   Route::post('/profile/updatebiodata', [ProfileController::class, 'updateBiodata'])->name('api.profile.updatebiodata');
   Route::post('/profile/gantipassword', [ProfileController::class, 'gantiPassword'])->name('api.profile.gantipassword');

   // api destinasi


   // api mytiket

});

// api data  lain