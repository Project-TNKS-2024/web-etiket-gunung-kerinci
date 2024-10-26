<?php

use Illuminate\Support\Facades\Route;


//admin dash
use App\Http\Controllers\etiket\admin\dashboard as dasAdmin;
// addmin destinasi
use App\Http\Controllers\etiket\admin\destinasi\destinasi;
use App\Http\Controllers\etiket\admin\destinasi\tiketController;




use App\Http\Controllers\etiket\admin\destinasis\booking as AdminBooking;
use App\Http\Controllers\etiket\admin\destinasis\tikets;
use App\Http\Controllers\etiket\admin\destinasis\gates;

// admin fitur
use App\Http\Controllers\etiket\admin\fitur\Scan;

// admin master


// Admin routes
Route::middleware(['check.role:admin'])->group(function () {
   Route::get('admin', function () {
      return redirect('admin/dashboard');
   });

   Route::get('admin/dashboard', [dasAdmin::class, 'index'])->name('admin.dashboard');

   // Destinasi - destinasi
   Route::get('admin/destinasi/{id}/detail', [destinasi::class, 'detail'])->name('admin.destinasi.detail');
   Route::post('admin/deatinasi/{id}/detail/update', [destinasi::class, 'detailUpdate'])->name('admin.destinasi.detail.update');
   Route::post('admin/destinasi/{id}/detail/picture/addAction', [destinasi::class, 'pictureAddAction'])->name('admin.destinasi.picture.add.action');

   // Destinasi - tiket
   Route::get('admin/destinasi/{id}/tiket', [tiketController::class, 'tiket'])->name('admin.destinasi.tiket');
   Route::get('admin/destinasi/{id}/tiket/add', [tiketController::class, 'add'])->name('admin.destinasi.tiket.add');

   // Destinasi - gate

   // Destinasi - booking

   // Destinasi - pendaki

   // Destinasi - monitoring

   // Destinasi - laporan


   // Destinasi - sop


   //tiket
   // Route::get('admin/kelola/tiket', [tikets::class, 'daftar'])->name('admin.tiket.daftar');
   // Route::get('admin/kelola/tambah-tiket', [tikets::class, 'tambah'])->name('admin.tiket.tambah');
   Route::get('admin/kelola/edit-tiket/{id}', [tikets::class, 'edit'])->name('admin.tiket.edit');
   Route::post('admin/kelola/tambah-tiket', [tikets::class, 'tambahAction'])->name('admin.tiket.tambahAction');
   Route::post('admin/kelola/edit-tiket/{id}', [tikets::class, 'editAction'])->name('admin.tiket.editAction');
   Route::post('admin/kelola/hapus-tiket/{id}', [tikets::class, 'hapus'])->name('admin.tiket.hapus');

   //sampel destinasi
   Route::get('admin/kelola/destinasi', [destinasis::class, 'daftar'])->name('admin.destinasi.daftar');
   Route::get('admin/kelola/tambah-destinasi', [destinasis::class, 'tambah'])->name('admin.destinasi.tambah');
   Route::post('admin/kelola/tambah-destinasi', [destinasis::class, 'tambahAction'])->name('admin.destinasi.tambahAction');
   // Route::post('admin/kelola/edit-destinasi/{id}', [destinasis::class, 'editAction'])->name('admin.destinasi.editAction');
   Route::post('admin/kelola/hapus-destinasi/{id}', [destinasis::class, 'hapus'])->name('admin.destinasi.hapus');
   // Route::post('admin/kelola/upload-destinasi/{id}', [destinasis::class, 'upload'])->name('admin.destinasi.upload');

   //gate
   Route::get('admin/kelola/gate', [gates::class, 'daftar'])->name('admin.gate.daftar');
   Route::get('admin/kelola/tambah-gate', [gates::class, 'tambah'])->name('admin.gate.tambah');
   Route::get('admin/kelola/edit-gate/{id}', [gates::class, 'edit'])->name('admin.gate.edit');
   Route::post('admin/kelola/tambah-gate', [gates::class, 'tambahAction'])->name('admin.gate.tambahAction');
   Route::post('admin/kelola/edit-gate/{id}', [gates::class, 'editAction'])->name('admin.gate.editAction');
   Route::post('admin/kelola/hapus-gate/{id}', [gates::class, 'hapus'])->name('admin.gate.hapus');

   // booking
   Route::get('admin/kelola/booking', [AdminBooking::class, 'readNow'])->name('admin.booking.now.read');



   Route::get('admin/fitur/scanTiket', [Scan::class, 'index'])->name('admin.fitur.scanTiket');
   Route::get('admin/fitur/DetailTiket/{uq}', [Scan::class, 'detailtiket'])->name('admin.fitur.detailTiket');
});
