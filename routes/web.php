<?php
//builtin

use Illuminate\Support\Facades\Route;
//homepage
use App\Http\Controllers\homepage\HomepageController;
use App\Http\Controllers\homepage\booking;
use Illuminate\Http\Request;

Route::get('/', function () {
    return redirect('beranda');
});

Route::get('beranda', [HomepageController::class, 'beranda'])->name('homepage.beranda');
Route::get('panduan', [HomepageController::class, 'panduan'])->name('homepage.panduan');
// sop per destinasi
Route::get('sop', [HomepageController::class, 'sop'])->name('homepage.sop');
// vr
// Route::get('virtualTour/{id}', [HomepageController::class, 'vr'])->name('homepage.vr');


// destinasi list
Route::get('booking/destinasi/list', [booking::class, 'destinasiList'])->name('homepage.booking.destinasi.list');
// destinasi paket
Route::get('booking/destinasi/{id}/paket', [booking::class, 'destinasiPaket'])->name('homepage.booking.destinasi.paket');
// destinasi tiket
Route::get('booking/destinasi/paket/{id}/tiket', [booking::class, 'destinasiTiket'])->name('homepage.booking.destinasi.paket.tiket');
Route::post('booking/destinasi/paket/tiket', [booking::class, 'destinasiTiketStore'])->name('homepage.booking.destinasi.paket.tiket.action');

// booking
Route::middleware('auth')->group(function () {
    Route::get('booking/{id}', [booking::class, 'bookingId'])->name('homepage.booking');

    Route::get('booking/{id}/snk', [booking::class, 'bookingSnk'])->name('homepage.booking.snk');
    Route::post('booking/snk', [booking::class, 'bookingSnkStore'])->name('homepage.booking.snk.action');

    Route::get('booking/{id}/formulir', [booking::class, 'bookingFP'])->name('homepage.booking.formulir');
    Route::post('booking/formulir/pendaki/add', [booking::class, 'bookingPendakiAdd'])->name('homepage.booking.formulir.pebdaki.add');
    Route::post('booking/formulir', [booking::class, 'bookingFPStore'])->name('homepage.booking.formulir.action');

    Route::get('booking/{id}/detail', [booking::class, 'bookingDetail'])->name('homepage.booking.detail');


    Route::get('booking/{id}/payment', [booking::class, 'bookingPayment'])->name('homepage.booking.payment');
    Route::get('tiket/{id}', [booking::class, 'tiket'])->name('dashboard.tiket');
});

include __DIR__ . '/web/routeAuth.php';
include __DIR__ . '/web/routeAdmins.php';
include __DIR__ . '/web/routeUsers.php';

//test
Route::get('/tes', function () {
    return view('test');
});
Route::get('/unauthorized', function () {
    return view('errors.abort');
});
