<?php

use App\Http\Controllers\helper\getDomisiliController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// helper
// getlistprovinsi
Route::get('domisili/list/provinsi', [getDomisiliController::class, 'getProvinsi']);
// getlistkabupaten
Route::get('domisili/list/kabupaten/{id}', [getDomisiliController::class, 'getKabupaten']);
// getlistkecamatan
Route::get('domisili/list/kecamatan/{id}', [getDomisiliController::class, 'getKecamatan']);
// getlistkelurahan
Route::get('domisili/list/kelurahan/{id}', [getDomisiliController::class, 'getKelurahan']);

// getdetailprofinsi
Route::get('domisili/detail/provinsi/{id}', [getDomisiliController::class, 'getProvinsiById']);
// getdetailkabupaten
Route::get('domisili/detail/kabupaten/{id}', [getDomisiliController::class, 'getKabupatenById']);
// getdetailkecamatan
Route::get('domisili/detail/kecamatan/{id}', [getDomisiliController::class, 'getKecamatanById']);
// getdetailkelurahan
Route::get('domisili/detail/kelurahan/{id}', [getDomisiliController::class, 'getKelurahanById']);
