<?php

use Illuminate\Support\Facades\Route;

//admin dash
use App\Http\Controllers\etiket\admin\dashboard as dasAdmin;
// addmin destinasi
use App\Http\Controllers\etiket\admin\destinasi\destinasiController;
use App\Http\Controllers\etiket\admin\destinasi\tiketController;
use App\Http\Controllers\etiket\admin\master\destinasisController;
use App\Http\Controllers\etiket\admin\destinasi\bookingController;

// admin fitur
use App\Http\Controllers\etiket\admin\fitur\Scan;
use App\Http\Controllers\etiket\admin\master\PengunjungController;

// admin master

// Admin routes
Route::middleware(['check.role:admin'])->group(function () {
    Route::get('admin', function () {
        return redirect('admin/dashboard');
    });

    Route::get('admin/dashboard', [dasAdmin::class, 'index'])->name('admin.dashboard');

    // Destinasi - destinasi
    Route::get('admin/destinasi/{id}/detail', [destinasiController::class, 'detail'])->name('admin.destinasi.detail');
    Route::post('admin/deatinasi/{id}/detail/update', [destinasiController::class, 'detailUpdate'])->name('admin.destinasi.detail.update');
    Route::post('admin/destinasi/detail/picture/add', [destinasiController::class, 'pictureAddAction'])->name('admin.destinasi.picture.addAction');
    Route::post('admin/destinasi/detail/picture/delete', [destinasiController::class, 'pictureDeleteAction'])->name('admin.destinasi.picture.deleteAction');
    Route::post('admin/destinasi/gates/add', [destinasiController::class, 'gatesAddAction'])->name('admin.destinasi.gates.addAction');
    Route::get('admin/destinasi/gates/{id}/update', [destinasiController::class, 'gatesUpdate'])->name('admin.destinasi.gates.update');
    Route::post('admin/destinasi/gates/update', [destinasiController::class, 'gatesUpdateAction'])->name('admin.destinasi.gates.updateAction');
    Route::post('admin/destinasi/gates/delete', [destinasiController::class, 'gatesDeleteAction'])->name('admin.destinasi.gates.deleteAction');

    // Destinasi - tiket
    Route::get('admin/destinasi/{id}/tiket', [tiketController::class, 'tiket'])->name('admin.destinasi.tiket');
    Route::get('admin/destinasi/{id}/tiket/add', [tiketController::class, 'add'])->name('admin.destinasi.tiket.add');
    Route::post('admin/destinasi/tiket/add', [tiketController::class, 'addAction'])->name('admin.destinasi.tiket.addAction');
    Route::get('admin/destinasi/tiket/{id}/uppdate', [tiketController::class, 'update'])->name('admin.destinasi.tiket.update');
    Route::post('admin/destinasi/tiket/uppdate', [tiketController::class, 'uppdateAction'])->name('admin.destinasi.tiket.uppdateAction');
    Route::post('admin/destinasi/tiket/delete', [tiketController::class, 'deleteAction'])->name('admin.destinasi.tiket.deleteAction');

    // Destinasi - booking
    Route::get('admin/destinasi/{id}/booking', [bookingController::class, 'index'])->name('admin.destinasi.booking');

    // Destinasi - pendaki

    // Destinasi - monitoring

    // Destinasi - laporan

    // Destinasi - sop

    // Master - Destinasi
    Route::get('admin/master/destinasi', [destinasisController::class, 'index'])->name('admin.master.destinasi');
    Route::get('admin/master/destinasi/add', [destinasisController::class, 'add'])->name('admin.master.destinasi.add');
    Route::post('admin/master/destinasi/add', [destinasisController::class, 'addAction'])->name('admin.master.destinasi.addAction');
    Route::post('admin/master/destinasi/delete', [destinasisController::class, 'deleteAction'])->name('admin.master.destinasi.deleteAction');

    // Master - Validasi Pembayaran
    Route::get('admin/master/validasi-pembayaran', [ValidasiPembayaran::class, 'index'])->name('admin.master.validasi.daftar');
    Route::get('admin/master/validasi-pembayaran?start_date={start_date?}&end_date={end_date?}&status={status?}', [ValidasiPembayaran::class, 'index'])->name('admin.master.validasi.daftar.filtered');
    Route::post('admin/master/validasi-pembayaran/update/', [ValidasiPembayaran::class, 'updateAction'])->name('admin.master.validasi.updateAction');

    // Master - Pengunjung
    Route::get('admin/master/pengunung', [PengunjungController::class, 'index'])->name('admin.master.pengunjung');
    Route::post('admin/master/pengunung/verified', [PengunjungController::class, 'verificationBiodata'])->name('admin.master.pengunjung.verified');

    // booking
    // Route::get('admin/kelola/booking', [AdminBooking::class, 'readNow'])->name('admin.booking.now.read');

    // fitur - scan tiket
    Route::get('admin/fitur/scanTiket', [Scan::class, 'index'])->name('admin.fitur.scanTiket');
    Route::get('admin/fitur/DetailTiket/{uq}', [Scan::class, 'detailtiket'])->name('admin.fitur.detailTiket');
});
