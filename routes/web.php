<?php
//admin

use App\Http\Controllers\etiket\admin\booking as AdminBooking;
use App\Http\Controllers\etiket\admin\dashboard as dasAdmin;
use App\Http\Controllers\etiket\admin\tikets;
use App\Http\Controllers\etiket\admin\destinasis;
use App\Http\Controllers\etiket\admin\gates;

//user
use App\Http\Controllers\etiket\user\dashboard as dasUser;
use App\Http\Controllers\etiket\user\profile as profileUser;
use App\Http\Controllers\etiket\user\gantipassword as resetPasswordUser;
use App\Http\Controllers\etiket\user\riwayat as riwayatUser;

//auth
use App\Http\Controllers\etiket\in\login;
use App\Http\Controllers\etiket\in\register;
use App\Http\Controllers\etiket\in\lupapassword;
use App\Http\Controllers\etiket\in\resetpassword;
use App\Http\Controllers\helper\BookingHelperController;
//homepage
use App\Http\Controllers\homepage\beranda;
use App\Http\Controllers\homepage\panduan;
use App\Http\Controllers\homepage\booking;

//~
use App\Http\Controllers\sampel;

//builtin
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('beranda');
});

Route::get('beranda', [beranda::class, 'index'])->name('homepage.beranda');
Route::get('sop', [panduan::class, 'sop'])->name('homepage.sop');
Route::get('panduan', [panduan::class, 'panduan'])->name('homepage.panduan');

Route::get('booking/{id}', [booking::class, 'booking'])->name('homepage.booking');
Route::get('booking/paket/{id}', [booking::class, 'bookingPaket'])->name('homepage.bookingpaket');
Route::post('booking/tiket', [booking::class, 'postBooking'])->name('homepage.postBooking');

Route::middleware('auth')->group(function () {
    // booking
    Route::get('booking/{id}/snk', [booking::class, 'bookingSnk'])->name('homepage.booking-snk');
    Route::post('booking/snk', [booking::class, 'bookingSnkStore'])->name('homepage.booking-snk.store');
    Route::get('booking/{id}/formulir', [booking::class, 'bookingFP'])->name('homepage.booking-fp');
    Route::post('booking/formulir', [booking::class, 'bookingFPStore'])->name('homepage.booking-fp.store');
    Route::get('booking/{id}/detail', [booking::class, 'bookingDetail'])->name('homepage.booking-detail');

    Route::get('booking/{id}/payment', [booking::class, 'bookingPayment'])->name('homepage.booking.payment');
    Route::get('booking/{id}/tiket', [booking::class, 'tiketBooking'])->name('homepage.booking.tiket');
    // logout
    Route::post('logout', [login::class, 'logout'])->name('etiket.in.logout');
});

Route::middleware('guest')->group(function () {
    // login register
    Route::get('login', [login::class, 'login'])->name('login');
    Route::post('login', [login::class, 'actionlogin'])->name('etiket.in.actionlogin');
    Route::get('register', [register::class, 'register'])->name('etiket.in.register');
    Route::post('register', [register::class, 'actionregister'])->name('etiket.in.actionregister');

    // lupa password
    Route::get('lupaPassword/sentEmail', [login::class, 'lp_sentEmail'])->name('etiket.in.lp.sentEmail');
    // Route::get('lupaPassword/confirmEmail', [login::class, 'lp_confirmEmail'])->name('etiket.in.lp.confirmEmail');
    Route::post('lupaPassword/confirmEmail', [login::class, 'lp_confirmEmail'])->name('etiket.in.lp.confirmEmail');
    // route::get('lupa-password', [lupapassword::class, 'lupapassword'])->name('etiket.in.lupapassword');
    // route::post('lupa-password', [lupapassword::class, 'actionlupapassword'])->name('etiket.in.actionlupapassword');
    route::get('reset-password/{token}', [resetpassword::class, 'resetpassword'])->name('etiket.in.resetpassword');
    route::post('reset-password/action/{token}/', [resetpassword::class, 'actionresetpassword'])->name('etiket.in.actionresetpassword');
});

// Admin routes
Route::middleware(['check.role:admin'])->group(function () {
    Route::get('admin', function () {
        return redirect('admin/dashboard');
    });

    Route::get('admin/dashboard', [dasAdmin::class, 'index'])->name('admin.dashboard');

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
});

// User routes
Route::middleware(['check.role:user'])->group(function () {

    Route::get('dashboard', [dasUser::class, 'index'])->name('user.dashboard');
    Route::get('dashboard/profile', [profileUser::class, 'index'])->name('user.dashboard.profile');
    Route::post('dashboard/profile/{id}', [profileUser::class, 'action'])->name('user.dashboard.action');
    Route::get('dashboard/riwayat-booking', [riwayatUser::class, 'index'])->name('user.dashboard.riwayat');

    //reset password
    Route::get('dashboard/ganti-password', [resetPasswordUser::class, 'index'])->name('user.dashboard.reset-password');
    Route::post('dashboard/reset-password', [resetPasswordUser::class, 'resetAction'])->name('user.dashboard.reset-password-action');
});


// sampel
// Route::get('sampel/{blade}', [sampel::class, 'index'])->name('sampel.index');
// Route::get('sampel/tesnotif', [sampel::class, 'tesnotif'])->name('sampel.tesnotif');
Route::get('sampel/tess/{id}', [BookingHelperController::class, 'validatPendaki'])->name('sampel.tesnotif');
// Route::post('sampel/tesnotif', [sampel::class, 'actiontesnotif'])->name('sampel.actiontesnotif');


//test
Route::get('/unauthorized', function () {
    // return view('errors.abort');
    return redirect('beranda');
});
