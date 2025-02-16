<?php

use Illuminate\Support\Facades\Route;

//admin dash
use App\Http\Controllers\homepage\booking;
// addmin destinasi
use App\Http\Controllers\etiket\admin\fitur\Scan;
use App\Http\Controllers\etiket\admin\settingController;
use App\Http\Controllers\etiket\admin\dashboard as dasAdmin;
use App\Http\Controllers\etiket\admin\destinasi\sopController;
use App\Http\Controllers\etiket\admin\destinasi\tiketController;
use App\Http\Controllers\etiket\admin\master\ValidasiPembayaran;
// admin fitur
use App\Http\Controllers\etiket\admin\destinasi\bookingController;
use App\Http\Controllers\etiket\admin\master\destinasisController;
use App\Http\Controllers\etiket\admin\master\PengunjungController;
use App\Http\Controllers\tracking\admin\checkpoint\CheckpointController;

// admin master
use App\Http\Controllers\etiket\admin\master\ValidasiPembayaranOld;
use App\Http\Controllers\etiket\admin\destinasi\destinasiController;
use App\Http\Controllers\etiket\admin\destinasi\pembayaranController;

// Admin routes
Route::middleware(['check.role:admin'])->group(function () {
    Route::get('admin', function () {
        return redirect('admin/dashboard');
    });

    Route::get('admin/dashboard', [dasAdmin::class, 'index'])->name('admin.dashboard');

    // Destinasi - destinasi
    Route::get('admin/destinasi/{id}/detail', [destinasiController::class, 'detail'])->name('admin.destinasi.detail');
    // picture
    Route::post('admin/destinasi/detail/picture/add', [destinasiController::class, 'pictureAddAction'])->name('admin.destinasi.picture.addAction');
    Route::post('admin/destinasi/detail/picture/delete', [destinasiController::class, 'pictureDeleteAction'])->name('admin.destinasi.picture.deleteAction');
    // gates
    Route::post('admin/destinasi/gates/add', [destinasiController::class, 'gatesAddAction'])->name('admin.destinasi.gates.addAction');
    Route::get('admin/destinasi/gates/{id}/update', [destinasiController::class, 'gatesUpdate'])->name('admin.destinasi.gates.update');
    Route::post('admin/destinasi/gates/update', [destinasiController::class, 'gatesUpdateAction'])->name('admin.destinasi.gates.updateAction');
    Route::post('admin/destinasi/gates/delete', [destinasiController::class, 'gatesDeleteAction'])->name('admin.destinasi.gates.deleteAction');
    // destinasi
    Route::get('admin/destinasi/{id}/update', [destinasiController::class, 'destinasiUpdate'])->name('admin.destinasi.update');
    Route::post('admin/destinasi/update', [destinasiController::class, 'destinasiUpdateAction'])->name('admin.destinasi.update.action');

    // Destinasi - tiket
    Route::get('admin/destinasi/{id}/tiket', [tiketController::class, 'tiket'])->name('admin.destinasi.tiket');
    Route::get('admin/destinasi/{id}/tiket/add', [tiketController::class, 'add'])->name('admin.destinasi.tiket.add');
    Route::post('admin/destinasi/tiket/add', [tiketController::class, 'addAction'])->name('admin.destinasi.tiket.addAction');
    Route::get('admin/destinasi/tiket/{id}/uppdate', [tiketController::class, 'update'])->name('admin.destinasi.tiket.update');
    Route::post('admin/destinasi/tiket/uppdate', [tiketController::class, 'uppdateAction'])->name('admin.destinasi.tiket.uppdateAction');
    Route::post('admin/destinasi/tiket/delete', [tiketController::class, 'deleteAction'])->name('admin.destinasi.tiket.deleteAction');

    // Destinasi - booking
    Route::get('admin/destinasi/{id}/booking', [bookingController::class, 'index'])->name('admin.destinasi.booking');
    Route::get('admin/destinasi/booking/{id}', [bookingController::class, 'showBooking'])->name('admin.destinasi.booking.show');

    // Destinasi - pembayaran
    Route::get('admin/destinasi/{id}/pembayaran', [pembayaranController::class, 'index'])->name('admin.destinasi.pembayaran');
    Route::post('admin/destinasi/pembayaran/update', [pembayaranController::class, 'updateAction'])->name('admin.destinasi.pembayaran.updateAction');

    // Destinasi - pendaki

    // Destinasi - monitoring


    // Master - Destinasi
    Route::get('admin/master/destinasi', [destinasisController::class, 'index'])->name('admin.master.destinasi');
    Route::get('admin/master/destinasi/add', [destinasisController::class, 'add'])->name('admin.master.destinasi.add');
    Route::post('admin/master/destinasi/add', [destinasisController::class, 'addAction'])->name('admin.master.destinasi.addAction');
    Route::post('admin/master/destinasi/delete', [destinasisController::class, 'deleteAction'])->name('admin.master.destinasi.deleteAction');

    // Master - Pengunjung
    Route::get('admin/master/pengunung', [PengunjungController::class, 'index'])->name('admin.master.pengunjung');
    Route::post('admin/master/pengunung/verified', [PengunjungController::class, 'verificationBiodata'])->name('admin.master.pengunjung.verified');



    // fitur - scan tiket
    Route::get('admin/fitur/scanTiket', [Scan::class, 'index'])->name('admin.fitur.scanTiket');
    Route::get('admin/fitur/DetailTiket/{uq}', [Scan::class, 'detailtiket'])->name('admin.fitur.detailTiket');


    // setting
    Route::get('admin/setting', [settingController::class, 'index'])->name('admin.setting');
    Route::get('admin/setting/add', [settingController::class, 'add'])->name('admin.setting.add');
    Route::post('admin/setting/addAction', [settingController::class, 'addAction'])->name('admin.setting.addAction');
    Route::get('admin/setting/{id}/update', [settingController::class, 'update'])->name('admin.setting.update');
    Route::post('admin/setting/updateAction', [settingController::class, 'updateAction'])->name('admin.setting.updateAction');
    Route::post('admin/setting/deleteAction', [settingController::class, 'deleteAction'])->name('admin.setting.deleteAction');

    // checkpoint
    Route::get('admin/kelola/chekpoint', [CheckpointController::class, 'daftar'])->name('admin.checkpoint.daftar');
    Route::get('admin/kelola/tambah-chekpoint', [CheckpointController::class, 'tambah'])->name('admin.checkpoint.tambah');
    Route::post('admin/kelola/tambah-checkoint', [CheckpointController::class, 'tambahAction'])->name('admin.checkpoint.tambahAction');
    Route::get('admin/kelola/edit-checkpoint/{id}', [CheckpointController::class, 'edit'])->name('admin.checkpoint.edit');
    Route::post('admin/kelola/edit-checkpoint/{id}', [CheckpointController::class, 'editAction'])->name('admin.checkpoint.editAction');
    Route::post('admin/kelola/hapus-checkpoint/{id}', [CheckpointController::class, 'hapus'])->name('admin.checkpoint.hapus');
});
