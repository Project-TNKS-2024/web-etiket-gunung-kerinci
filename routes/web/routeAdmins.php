<?php

use Illuminate\Support\Facades\Route;

//admin dash
use App\Http\Controllers\etiket\admin\dashboard as dasAdmin;
// addmin destinasi
use App\Http\Controllers\etiket\admin\destinasi\destinasiController;
use App\Http\Controllers\etiket\admin\destinasi\tiketController;
use App\Http\Controllers\etiket\admin\master\destinasisController;
use App\Http\Controllers\etiket\admin\destinasi\bookingController;
use App\Http\Controllers\etiket\admin\destinasi\pembayaranController;
// admin fitur
use App\Http\Controllers\etiket\admin\fitur\Scan;
use App\Http\Controllers\etiket\admin\master\AccountAdminController;
use App\Http\Controllers\etiket\admin\master\PengunjungController;
use App\Http\Controllers\etiket\admin\master\RolePermissionController;
// admin master
use App\Http\Controllers\etiket\admin\settingController;

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
    Route::post('admin/destinasi/booking/updateStatus', [bookingController::class, 'updateStatus'])->name('admin.destinasi.booking.updateStatus');

    Route::get('admin/destinasi/booking/{id}/pembayaran', [bookingController::class, 'showPembayaran'])->name('admin.destinasi.booking.payment.show');
    Route::post('admin/destinasi/booking/pembayaran/update', [bookingController::class, 'updatePembayaran'])->name('admin.destinasi.booking.payment.update');

    Route::get('admin/destinasi/booking/{id}/tiket', [bookingController::class, 'showTiket'])->name('admin.destinasi.booking.tiket.show');
    Route::get('admin/destinasi/booking/{id}/struk', [bookingController::class, 'showStruk'])->name('admin.destinasi.booking.struk.show');

    // Destinasi - pendaki


    // Master - Destinasi
    Route::get('admin/master/destinasi', [destinasisController::class, 'index'])->name('admin.master.destinasi');
    Route::get('admin/master/destinasi/add', [destinasisController::class, 'add'])->name('admin.master.destinasi.add');
    Route::post('admin/master/destinasi/add', [destinasisController::class, 'addAction'])->name('admin.master.destinasi.addAction');
    Route::post('admin/master/destinasi/delete', [destinasisController::class, 'deleteAction'])->name('admin.master.destinasi.deleteAction');

    // Master - Pengunjung
    Route::get('admin/master/pengujung', [PengunjungController::class, 'index'])->name('admin.master.pengunjung');
    Route::get('admin/master/pengujung/{id}/biodata', [PengunjungController::class, 'biodata'])->name('admin.master.pengunjung.biodata');
    Route::post('admin/master/pengujung/biodata/verified', [PengunjungController::class, 'verificationBiodata'])->name('admin.master.pengunjung.biodata.verified');

    // Master - Admin
    Route::get('/admins/akun', [AccountAdminController::class, 'index'])->name('admins.akun.index');
    Route::get('/admins/akun/create', [AccountAdminController::class, 'create'])->name('admins.akun.create');
    Route::post('/admins/akun/store', [AccountAdminController::class, 'store'])->name('admins.akun.store');
    Route::get('/admins/akun/edit/{id}', [AccountAdminController::class, 'edit'])->name('admins.akun.edit');
    Route::post('/admins/akun/update/{id}', [AccountAdminController::class, 'update'])->name('admins.akun.update');
    Route::post('/admins/akun/delete', [AccountAdminController::class, 'destroy'])->name('admins.akun.delete');

    //  Master - Role & User
    Route::get('/roles', [RolePermissionController::class, 'index'])->name('roles.index');
    Route::post('/roles/add', [RolePermissionController::class, 'roleAddAction'])->name('roles.addAction');
    Route::post('/permissions/add', [RolePermissionController::class, 'permissionAddAction'])->name('permissions.addAction');
    Route::get('/roles/{id}/update', [RolePermissionController::class, 'rolesUpdate'])->name('roles.update');
    Route::post('/roles/updateAction', [RolePermissionController::class, 'rolesUpdateAction'])->name('roles.updateAction');
    Route::post('/roles/deleteAction', [RolePermissionController::class, 'roleDeleteAction'])->name('roles.deleteAction');




    // fitur - scan tiket
    Route::get('admin/fitur/scanTiket', [Scan::class, 'index'])->name('admin.fitur.scanTiket');
    Route::get('admin/fitur/scanTiketAction/{uq}', [Scan::class, 'scanTiketAction'])->name('admin.fitur.scanTiketAction');


    // setting
    Route::get('admin/setting', [settingController::class, 'index'])->name('admin.setting');
    Route::get('admin/setting/add', [settingController::class, 'add'])->name('admin.setting.add');
    Route::post('admin/setting/addAction', [settingController::class, 'addAction'])->name('admin.setting.addAction');
    Route::get('admin/setting/{id}/update', [settingController::class, 'update'])->name('admin.setting.update');
    Route::post('admin/setting/updateAction', [settingController::class, 'updateAction'])->name('admin.setting.updateAction');
    Route::post('admin/setting/deleteAction', [settingController::class, 'deleteAction'])->name('admin.setting.deleteAction');
});
