<?php

use App\Http\Controllers\helper\DomisiliController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// helper
// getlistprovinsi
Route::get('domisili/list/provinsi', [DomisiliController::class, 'getProvinsi']);
// getlistkabupaten
Route::get('domisili/list/kabupaten/{id}', [DomisiliController::class, 'getKabupaten']);
// getlistkecamatan
Route::get('domisili/list/kecamatan/{id}', [DomisiliController::class, 'getKecamatan']);
// getlistkelurahan
Route::get('domisili/list/kelurahan/{id}', [DomisiliController::class, 'getKelurahan']);

// getdetailprofinsi
Route::get('domisili/detail/provinsi/{id}', [DomisiliController::class, 'getProvinsiById']);
// getdetailkabupaten
Route::get('domisili/detail/kabupaten/{id}', [DomisiliController::class, 'getKabupatenById']);
// getdetailkecamatan
Route::get('domisili/detail/kecamatan/{id}', [DomisiliController::class, 'getKecamatanById']);
// getdetailkelurahan
Route::get('domisili/detail/kelurahan/{id}', [DomisiliController::class, 'getKelurahanById']);
