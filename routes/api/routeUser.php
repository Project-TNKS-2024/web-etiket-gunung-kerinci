<?php

use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\DestinasiController;
use App\Http\Controllers\API\BookingController;
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
    Route::get('/destinasi/{id}', [DestinasiController::class, 'show'])->whereNumber('id')->name('api.destinasi.show');
    Route::get('/destinasi/{id}/paket', [DestinasiController::class, 'paket'])->whereNumber('id')->name('api.destinasi.paket');
    Route::get('/destinasi/paket/{id}/tiket', [DestinasiController::class, 'tiket'])->whereNumber('id')->name('api.destinasi.paket.tiket');


    // api mytiket
    Route::post('/booking/destinasi/paket/tiket', [BookingController::class, 'store'])->name('api.booking.store');
    Route::get('/booking/{id}', [BookingController::class, 'show'])->whereUuid('id')->name('api.booking.show');
    Route::post('/booking/snk', [BookingController::class, 'acceptSnk'])->name('api.booking.snk');
    Route::get('/booking/{id}/formulir', [BookingController::class, 'formulir'])->whereUuid('id')->name('api.booking.formulir');
    Route::post('/booking/formulir/pendaki/add', [BookingController::class, 'addPendaki'])->name('api.booking.formulir.pendaki.add');
    Route::post('/booking/formulir', [BookingController::class, 'saveFormulir'])->name('api.booking.formulir.save');
    Route::get('/booking/{id}/detail', [BookingController::class, 'detail'])->whereUuid('id')->name('api.booking.detail');
    Route::get('/booking/{id}/payment', [BookingController::class, 'payment'])->whereUuid('id')->name('api.booking.payment');
    Route::post('/booking/payment', [BookingController::class, 'addPayment'])->name('api.booking.payment.add');
    Route::delete('/booking/payment/{id}', [BookingController::class, 'deletePayment'])->whereUuid('id')->name('api.booking.payment.delete');
    Route::get('/booking/{id}/struk', [BookingController::class, 'struk'])->whereUuid('id')->name('api.booking.struk');
    Route::get('/booking/{id}/tiket', [BookingController::class, 'tiket'])->whereUuid('id')->name('api.booking.tiket');
    Route::delete('/booking/{id}', [BookingController::class, 'cancel'])->whereUuid('id')->name('api.booking.cancel');
    Route::get('/mytiket', [BookingController::class, 'myTickets'])->name('api.mytiket');

});

// api data  lain
