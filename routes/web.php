<?php
//builtin

use Illuminate\Support\Facades\Route;
//homepage
use App\Http\Controllers\etiket\HomepageController;
use App\Http\Controllers\homepage\booking;

Route::get('/', function () {
    return redirect('beranda');
});

Route::get('beranda', [HomepageController::class, 'beranda'])->name('homepage.beranda');
Route::get('sop', [HomepageController::class, 'sop'])->name('homepage.sop');
Route::get('panduan', [HomepageController::class, 'panduan'])->name('homepage.panduan');

Route::get('booking/{id}', [booking::class, 'booking'])->name('homepage.booking');
Route::get('booking/paket/{id}', [booking::class, 'bookingPaket'])->name('homepage.bookingpaket');
Route::post('booking/tiket', [booking::class, 'postBooking'])->name('homepage.postBooking');

// booking
Route::middleware('auth')->group(function () {
    Route::get('booking/{id}/snk', [booking::class, 'bookingSnk'])->name('homepage.booking-snk');
    Route::post('booking/snk', [booking::class, 'bookingSnkStore'])->name('homepage.booking-snk.store');
    Route::get('booking/{id}/formulir', [booking::class, 'bookingFP'])->name('homepage.booking-fp');
    Route::post('booking/formulir', [booking::class, 'bookingFPStore'])->name('homepage.booking-fp.store');
    Route::get('booking/{id}/detail', [booking::class, 'bookingDetail'])->name('homepage.booking-detail');

    Route::get('booking/{id}/payment', [booking::class, 'bookingPayment'])->name('homepage.booking.payment');
    Route::get('booking/{id}/tiket', [booking::class, 'tiketBooking'])->name('homepage.booking.tiket');
});
include __DIR__ . '/web/routeAuth.php';
include __DIR__ . '/web/routeAdmins.php';
include __DIR__ . '/web/routeUsers.php';

//test
Route::get('/unauthorized', function () {
    // return view('errors.abort');
    return redirect('beranda');
});
