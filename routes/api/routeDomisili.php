<?php

use App\Http\Controllers\API\DomisiliController;
use Illuminate\Support\Facades\Route;

Route::prefix('domisili')->group(function () {

   // ===== NEGARA =====
   Route::get('/negara', [DomisiliController::class, 'getNegara']);

   // ===== PROVINSI =====
   Route::get('/provinsi', [DomisiliController::class, 'getProvinsi']);
   Route::get('/provinsi/{id}', [DomisiliController::class, 'getProvinsiById']);

   // ===== KABUPATEN =====
   Route::get('/kabupaten/provinsi/{id}', [DomisiliController::class, 'getKabupatenByIdProvinsi']);
   Route::get('/kabupaten/{id}', [DomisiliController::class, 'getKabupatenById']);

   // ===== KECAMATAN =====
   Route::get('/kecamatan/kabupaten/{id}', [DomisiliController::class, 'getKecamatanByIdKabupaten']);
   Route::get('/kecamatan/{id}', [DomisiliController::class, 'getKecamatanById']);

   // ===== KELURAHAN / DESA =====
   Route::get('/kelurahan/kecamatan/{id}', [DomisiliController::class, 'getKelurahanByIdKecamatan']);
   Route::get('/kelurahan/{id}', [DomisiliController::class, 'getKelurahanById']);
});
