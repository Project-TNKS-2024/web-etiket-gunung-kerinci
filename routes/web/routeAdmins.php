<?php

use Illuminate\Support\Facades\Route;


//admin
use App\Http\Controllers\etiket\admin\destinasis\booking as AdminBooking;
use App\Http\Controllers\etiket\admin\dashboard as dasAdmin;
use App\Http\Controllers\etiket\admin\destinasis\tikets;
use App\Http\Controllers\etiket\admin\destinasis\destinasis;
use App\Http\Controllers\etiket\admin\fitur\Scan;
use App\Http\Controllers\etiket\admin\destinasis\gates;

// destinasi
use App\Http\Controllers\etiket\admin\destinasi\destinasi;


// Admin routes
Route::middleware(['check.role:admin'])->group(function () {
   Route::get('admin', function () {
      return redirect('admin/dashboard');
   });

   Route::get('admin/dashboard', [dasAdmin::class, 'index'])->name('admin.dashboard');

   // Destinasi - destinasi
   Route::get('admin/destinasi/{id}/detail', [destinasi::class, 'detail'])->name('admin.destinasi.detail');

   // Destinasi - sop

   // Destinasi - booking

   // Destinasi - pendaki

   // Destinasi - monitoring

   // Destinasi - laporan



   //tiket
   Route::get('admin/kelola/tiket', [tikets::class, 'daftar'])->name('admin.tiket.daftar');
   Route::get('admin/kelola/tambah-tiket', [tikets::class, 'tambah'])->name('admin.tiket.tambah');
   Route::get('admin/kelola/edit-tiket/{id}', [tikets::class, 'edit'])->name('admin.tiket.edit');
   Route::post('admin/kelola/tambah-tiket', [tikets::class, 'tambahAction'])->name('admin.tiket.tambahAction');
   Route::post('admin/kelola/edit-tiket/{id}', [tikets::class, 'editAction'])->name('admin.tiket.editAction');
   Route::post('admin/kelola/hapus-tiket/{id}', [tikets::class, 'hapus'])->name('admin.tiket.hapus');

   //destinasi
   Route::get('admin/kelola/destinasi', [destinasis::class, 'daftar'])->name('admin.destinasi.daftar');
   Route::get('admin/kelola/tambah-destinasi', [destinasis::class, 'tambah'])->name('admin.destinasi.tambah');
   Route::get('admin/kelola/edit-destinasi/{id}', [destinasis::class, 'edit'])->name('admin.destinasi.edit');
   Route::post('admin/kelola/tambah-destinasi', [destinasis::class, 'tambahAction'])->name('admin.destinasi.tambahAction');
   Route::post('admin/kelola/edit-destinasi/{id}', [destinasis::class, 'editAction'])->name('admin.destinasi.editAction');
   Route::post('admin/kelola/hapus-destinasi/{id}', [destinasis::class, 'hapus'])->name('admin.destinasi.hapus');
   Route::post('admin/kelola/upload-destinasi/{id}', [destinasis::class, 'upload'])->name('admin.destinasi.upload');

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
