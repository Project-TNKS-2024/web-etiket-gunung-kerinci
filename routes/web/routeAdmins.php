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
use App\Http\Controllers\etiket\admin\fitur\KalenderController;
use App\Http\Controllers\etiket\admin\fitur\LogController;
// admin fitur
use App\Http\Controllers\etiket\admin\fitur\Scan;
use App\Http\Controllers\etiket\admin\master\AccountAdminController;
use App\Http\Controllers\etiket\admin\master\PengunjungController;
use App\Http\Controllers\etiket\admin\master\RolePermissionController;
use App\Http\Controllers\etiket\admin\ProfileController;
use App\Http\Controllers\etiket\admin\rekapitulasi\pendapatan;
use App\Http\Controllers\etiket\admin\rekapitulasi\pengunjung;
// admin master
use App\Http\Controllers\etiket\admin\settingController;

// Admin routes
Route::middleware(['check.role:admin'])->group(function () {
    Route::get('admin', function () {
        return redirect('admin/dashboard');
    });

    Route::get('admin/dashboard', [dasAdmin::class, 'index'])->name('admin.dashboard')->middleware('permission:view-dashboard');

    // Destinasi - destinasi
    Route::get('admin/destinasi/{id}/detail', [destinasiController::class, 'detail'])->name('admin.destinasi.detail')->middleware('permission:view-destinasi');
    // picture
    Route::post('admin/destinasi/detail/picture/add', [destinasiController::class, 'pictureAddAction'])->name('admin.destinasi.picture.addAction')->middleware('permission:create-destinasi');
    Route::post('admin/destinasi/detail/picture/delete', [destinasiController::class, 'pictureDeleteAction'])->name('admin.destinasi.picture.deleteAction')->middleware('permission:delete-destinasi');
    // gates
    Route::post('admin/destinasi/gates/add', [destinasiController::class, 'gatesAddAction'])->name('admin.destinasi.gates.addAction')->middleware('permission:create-gates');
    Route::get('admin/destinasi/gates/{id}/update', [destinasiController::class, 'gatesUpdate'])->name('admin.destinasi.gates.update')->middleware('permission:edit-gates');
    Route::post('admin/destinasi/gates/update', [destinasiController::class, 'gatesUpdateAction'])->name('admin.destinasi.gates.updateAction')->middleware('permission:edit-gates');
    Route::post('admin/destinasi/gates/delete', [destinasiController::class, 'gatesDeleteAction'])->name('admin.destinasi.gates.deleteAction')->middleware('permission:delete-gates');
    // destinasi
    Route::get('admin/destinasi/{id}/update', [destinasiController::class, 'destinasiUpdate'])->name('admin.destinasi.update')->middleware('permission:edit-destinasi');
    Route::post('admin/destinasi/update', [destinasiController::class, 'destinasiUpdateAction'])->name('admin.destinasi.update.action')->middleware('permission:edit-destinasi');

    // Destinasi - tiket
    Route::get('admin/destinasi/{id}/tiket', [tiketController::class, 'tiket'])->name('admin.destinasi.tiket')->middleware('permission:view-tiket');
    Route::get('admin/destinasi/{id}/tiket/add', [tiketController::class, 'add'])->name('admin.destinasi.tiket.add')->middleware('permission:create-tiket');
    Route::post('admin/destinasi/tiket/add', [tiketController::class, 'addAction'])->name('admin.destinasi.tiket.addAction')->middleware('permission:create-tiket');
    Route::get('admin/destinasi/tiket/{id}/uppdate', [tiketController::class, 'update'])->name('admin.destinasi.tiket.update')->middleware('permission:edit-tiket');
    Route::post('admin/destinasi/tiket/uppdate', [tiketController::class, 'uppdateAction'])->name('admin.destinasi.tiket.uppdateAction')->middleware('permission:edit-tiket');
    Route::post('admin/destinasi/tiket/delete', [tiketController::class, 'deleteAction'])->name('admin.destinasi.tiket.deleteAction')->middleware('permission:delete-tiket');

    // Destinasi - booking
    Route::get('admin/destinasi/{id}/booking', [bookingController::class, 'index'])->name('admin.destinasi.booking')->middleware('permission:view-booking');
    Route::get('admin/destinasi/booking/{id}', [bookingController::class, 'showBooking'])->name('admin.destinasi.booking.show')->middleware('permission:view-booking');
    Route::post('admin/destinasi/booking/updateStatus', [bookingController::class, 'updateStatus'])->name('admin.destinasi.booking.updateStatus')->middleware('permission:edit-booking');
    Route::post('admin/destinasi/booking/gantiTanggal', [bookingController::class, 'gantiTanggal'])->name('admin.destinasi.booking.gantiTanggal')->middleware('permission:edit-booking');

    Route::get('admin/destinasi/booking/{id}/pembayaran', [bookingController::class, 'showPembayaran'])->name('admin.destinasi.booking.payment.show')->middleware('permission:view-payment');
    Route::post('admin/destinasi/booking/pembayaran/update', [bookingController::class, 'updatePembayaran'])->name('admin.destinasi.booking.payment.update')->middleware('permission:edit-payment');

    Route::get('admin/destinasi/booking/{id}/tiket', [bookingController::class, 'showTiket'])->name('admin.destinasi.booking.tiket.show')->middleware('permission:view-tiket');
    Route::get('admin/destinasi/booking/{id}/struk', [bookingController::class, 'showStruk'])->name('admin.destinasi.booking.struk.show')->middleware('permission:view-struk');

    // Master - Destinasi
    Route::get('admin/master/destinasi', [destinasisController::class, 'index'])->name('admin.master.destinasi')->middleware('permission:master-view-destinasi');
    Route::get('admin/master/destinasi/add', [destinasisController::class, 'add'])->name('admin.master.destinasi.add')->middleware('permission:master-create-destinasi');
    Route::post('admin/master/destinasi/add', [destinasisController::class, 'addAction'])->name('admin.master.destinasi.addAction')->middleware('permission:master-create-destinasi');
    Route::post('admin/master/destinasi/delete', [destinasisController::class, 'deleteAction'])->name('admin.master.destinasi.deleteAction')->middleware('permission:master-delete-destinasi');

    // Master - Pengunjung
    Route::get('admin/master/pengujung', [PengunjungController::class, 'index'])->name('admin.master.pengunjung')->middleware('permission:master-view-akunpengunjung');
    Route::get('admin/master/pengujung/{id}/biodata', [PengunjungController::class, 'biodata'])->name('admin.master.pengunjung.biodata')->middleware('permission:view-pengunjung');
    Route::post('admin/master/pengujung/biodata/verified', [PengunjungController::class, 'verificationBiodata'])->name('admin.master.pengunjung.biodata.verified')->middleware('permission:verify-akunpengunjung');

    // Master - Admin
    Route::get('/admins/akun', [AccountAdminController::class, 'index'])->name('admins.akun.index')->middleware('permission:master-view-admin');
    Route::get('/admins/akun/create', [AccountAdminController::class, 'create'])->name('admins.akun.create')->middleware('permission:master-create-admin');
    Route::post('/admins/akun/store', [AccountAdminController::class, 'store'])->name('admins.akun.store')->middleware('permission:master-create-admin');
    Route::get('/admins/akun/edit/{id}', [AccountAdminController::class, 'edit'])->name('admins.akun.edit')->middleware('permission:master-edit-admin');
    Route::post('/admins/akun/update/{id}', [AccountAdminController::class, 'update'])->name('admins.akun.update')->middleware('permission:master-edit-admin');
    Route::post('/admins/akun/delete', [AccountAdminController::class, 'destroy'])->name('admins.akun.delete')->middleware('permission:master-delete-admin');

    //  Master - Role & User
    Route::get('/roles', [RolePermissionController::class, 'index'])->name('roles.index')->middleware('permission:master-view-roles');
    Route::post('/roles/add', [RolePermissionController::class, 'roleAddAction'])->name('roles.addAction')->middleware('permission:master-create-roles');
    Route::post('/permissions/add', [RolePermissionController::class, 'permissionAddAction'])->name('permissions.addAction')->middleware('permission:master-create-permissions');
    Route::get('/roles/{id}/update', [RolePermissionController::class, 'rolesUpdate'])->name('roles.update')->middleware('permission:master-edit-roles');
    Route::post('/roles/updateAction', [RolePermissionController::class, 'rolesUpdateAction'])->name('roles.updateAction')->middleware('permission:master-edit-roles');
    Route::post('/roles/deleteAction', [RolePermissionController::class, 'roleDeleteAction'])->name('roles.deleteAction')->middleware('permission:master-delete-roles');

    // Rekaptulasi
    Route::get('admin/rekaptulasi/pendapatan', [pendapatan::class, 'index'])->name('admin.rekap.pendapatan')->middleware('permission:view-rekap-pendapatan');
    Route::get('admin/rekaptulasi/pendapatan/donwload', [pendapatan::class, 'download'])->name('admin.rekap.pendapatan.download')->middleware('permission:download-rekap-pendapatan');
    Route::get('admin/rekaptulasi/pengunjung', [pengunjung::class, 'index'])->name('admin.rekap.pengunjung')->middleware('permission:view-rekap-pengunjung');
    Route::get('admin/rekaptulasi/pengunjung/donwload', [pengunjung::class, 'download'])->name('admin.rekap.pengunjung.download')->middleware('permission:download-rekap-pengunjung');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('admin.profile')->middleware('permission:view-profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('admin.profile.action')->middleware('permission:view-profile');
    Route::get('/profile/reset-password', [ProfileController::class, 'resetPassword'])->name('admin.profile.resetPassword')->middleware('permission:reset-password');
    Route::post('/profile/reset-password', [ProfileController::class, 'resetPasswordAction'])->name('admin.profile.resetPassword.action')->middleware('permission:reset-password');

    // fitur - scan tiket
    Route::get('admin/fitur/scanTiket', [Scan::class, 'index'])->name('admin.fitur.scanTiket')->middleware('permission:scan-tiket');
    Route::get('admin/fitur/scanTiketAction/{uq}', [Scan::class, 'scanTiketAction'])->name('admin.fitur.scanTiketAction')->middleware('permission:scan-tiket');

    // fitur - log
    Route::get('admin/fitur/log', [LogController::class, 'index'])->name('admin.fitur.log')->middleware('permission:view-log');

    // fitur - kelender
    Route::get('admin/fitur/kalender', [KalenderController::class, 'index'])->name('admin.fitur.kalender')->middleware('permission:view-kalender');
    Route::post('/admin/fitur/kalender/events', [KalenderController::class, 'storeEvent'])->name('admin.fitur.kalender.storeEvent')->middleware('permission:create-kalender');
    Route::post('/admin/fitur/kalender/events/json', [KalenderController::class, 'storjsonEvent'])->name('admin.fitur.kalender.storejsonEvent')->middleware('permission:create-kalender');
    Route::post('/admin/fitur/kalender/events/delete', [KalenderController::class, 'destroyEvent'])->name('admin.fitur.kalender.destroyEvent')->middleware('permission:delete-kalender');



    // setting
    Route::get('admin/setting', [settingController::class, 'index'])->name('admin.setting')->middleware('permission:view-setting');
    Route::get('admin/setting/add', [settingController::class, 'add'])->name('admin.setting.add')->middleware('permission:create-setting');
    Route::post('admin/setting/addAction', [settingController::class, 'addAction'])->name('admin.setting.addAction')->middleware('permission:create-setting');
    Route::get('admin/setting/{id}/update', [settingController::class, 'update'])->name('admin.setting.update')->middleware('permission:edit-setting');
    Route::post('admin/setting/updateAction', [settingController::class, 'updateAction'])->name('admin.setting.updateAction')->middleware('permission:edit-setting');
    Route::post('admin/setting/deleteAction', [settingController::class, 'deleteAction'])->name('admin.setting.deleteAction')->middleware('permission:delete-setting');

    // Emergency Management
    Route::get('admin/emergency', [\App\Http\Controllers\etiket\admin\emergency\EmergencyAdminController::class, 'index'])->name('admin.emergency.index');
    Route::post('admin/emergency/broadcast', [\App\Http\Controllers\etiket\admin\emergency\EmergencyAdminController::class, 'broadcast'])->name('admin.emergency.broadcast');
    Route::put('admin/emergency/{id}/acknowledge', [\App\Http\Controllers\etiket\admin\emergency\EmergencyAdminController::class, 'acknowledge'])->name('admin.emergency.acknowledge');
    Route::put('admin/emergency/{id}/resolve', [\App\Http\Controllers\etiket\admin\emergency\EmergencyAdminController::class, 'resolve'])->name('admin.emergency.resolve');

    // Post (Checkpoint) Management
    Route::get('admin/posts', [\App\Http\Controllers\etiket\admin\posts\PostAdminController::class, 'index'])->name('admin.posts.index');
    Route::post('admin/posts', [\App\Http\Controllers\etiket\admin\posts\PostAdminController::class, 'store'])->name('admin.posts.store');
    Route::put('admin/posts/{id}', [\App\Http\Controllers\etiket\admin\posts\PostAdminController::class, 'update'])->name('admin.posts.update');
    Route::delete('admin/posts/{id}', [\App\Http\Controllers\etiket\admin\posts\PostAdminController::class, 'destroy'])->name('admin.posts.destroy');

    // SOS Management
    Route::get('admin/sos', [\App\Http\Controllers\etiket\admin\sos\SOSAdminController::class, 'index'])->name('admin.sos.index');
    Route::get('admin/sos/{id}', [\App\Http\Controllers\etiket\admin\sos\SOSAdminController::class, 'detail'])->name('admin.sos.detail');
    Route::put('admin/sos/{id}/status', [\App\Http\Controllers\etiket\admin\sos\SOSAdminController::class, 'updateStatus'])->name('admin.sos.updateStatus');
    Route::put('admin/disaster-report/{id}/verify', [\App\Http\Controllers\etiket\admin\sos\SOSAdminController::class, 'verifyDisasterReport'])->name('admin.disaster-report.verify');
});
