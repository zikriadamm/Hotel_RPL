<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\FrontDeskController;
use App\Http\Controllers\HousekeepingController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - HotelOps (Sistem Manajemen Operasional Hotel)
|--------------------------------------------------------------------------
*/

// Halaman Awal Hotel & Resor (Beranda)
Route::get('/', [LandingController::class, 'index'])->name('landing');

// HALAMAN KHUSUS FORMULIR PEMESANAN (Dedicated Booking Page)
Route::get('/booking', [BookingController::class, 'showForm'])->name('booking.form');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store')->middleware('auth');
Route::get('/booking/bayar/{pemesanan}', [BookingController::class, 'bayar'])->name('booking.bayar')->middleware('auth');
Route::get('/booking/status/{pemesanan}', [BookingController::class, 'statusPembayaran'])->name('booking.status')->middleware('auth');
Route::post('/booking/simulasi-bayar/{pemesanan}', [BookingController::class, 'simulasiBayar'])->name('booking.simulasi_bayar')->middleware('auth');
Route::post('/api/webhook/qris', [BookingController::class, 'webhookQris'])->name('api.webhook.qris');
Route::get('/booking/sukses/{pemesanan}', [BookingController::class, 'sukses'])->name('booking.sukses')->middleware('auth');

// ==========================================
// HALAMAN KHUSUS TAMU & PELANGGAN (Authenticated Portal)
// ==========================================
Route::middleware(['auth'])->prefix('pelanggan')->name('pelanggan.')->group(function () {
    Route::get('/kamar', [BookingController::class, 'kamarIndex'])->name('kamar');
    Route::get('/', [BookingController::class, 'kamarIndex'])->name('index');
    Route::get('/riwayat', [BookingController::class, 'dashboard'])->name('dashboard');
    Route::get('/booking', [BookingController::class, 'showForm'])->name('booking');
    Route::post('/pemesanan/{pemesanan}/batal', [BookingController::class, 'batalkanPemesanan'])->name('pemesanan.batal');

    // CRUD Profil Pelanggan (Read, Update Identitas, Update Password, Delete Akun)
    Route::get('/profil', [ProfileController::class, 'show'])->name('profil');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profil.update');
    Route::put('/profil/password', [ProfileController::class, 'updatePassword'])->name('profil.password');
    Route::delete('/profil', [ProfileController::class, 'destroy'])->name('profil.destroy');
});
Route::redirect('/tamu', '/pelanggan/kamar');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/demo-login/{peran}', [AuthController::class, 'demoLogin'])->name('demo.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// AREA RESEPSIONIS (Meja Depan & Kasir)
// ==========================================
Route::middleware(['auth', 'role:Resepsionis,Manajer'])->prefix('resepsionis')->name('resepsionis.')->group(function () {
    Route::get('/', [FrontDeskController::class, 'dashboard'])->name('dashboard');
    
    // Kelola Data Tamu
    Route::get('/tamu', [FrontDeskController::class, 'tamuIndex'])->name('tamu.index');
    Route::post('/tamu', [FrontDeskController::class, 'tamuStore'])->name('tamu.store');
    
    // Pemesanan & Reservasi
    Route::get('/pemesanan', [FrontDeskController::class, 'pemesananIndex'])->name('pemesanan.index');
    Route::post('/pemesanan', [FrontDeskController::class, 'pemesananStore'])->name('pemesanan.store');
    Route::post('/pemesanan/{pemesanan}/check-in', [FrontDeskController::class, 'checkIn'])->name('pemesanan.checkin');
    Route::post('/pemesanan/{pemesanan}/check-out', [FrontDeskController::class, 'checkOut'])->name('pemesanan.checkout');
    Route::post('/pemesanan/{pemesanan}/batal', [FrontDeskController::class, 'batalkan'])->name('pemesanan.batal');
    Route::get('/pemesanan/{pemesanan}/invoice', [FrontDeskController::class, 'invoice'])->name('invoice');
});

// ==========================================
// AREA PETUGAS KEBERSIHAN (Housekeeping)
// ==========================================
Route::middleware(['auth', 'role:Petugas Kebersihan,Manajer'])->prefix('kebersihan')->name('kebersihan.')->group(function () {
    Route::get('/', [HousekeepingController::class, 'index'])->name('index');
    Route::post('/{kamar}/bersih', [HousekeepingController::class, 'tandaiBersih'])->name('kamar.bersih');
    Route::post('/{kamar}/kotor', [HousekeepingController::class, 'tandaiKotor'])->name('kamar.kotor');
});

// ==========================================
// AREA MANAJER (Master Data & Laporan)
// ==========================================
Route::middleware(['auth', 'role:Manajer'])->group(function () {
    // Master Kamar
    Route::prefix('manajer')->name('manajer.')->group(function () {
        Route::get('/kamar', [ManagerController::class, 'kamarIndex'])->name('kamar.index');
        Route::post('/kamar', [ManagerController::class, 'kamarStore'])->name('kamar.store');
        Route::put('/kamar/{kamar}', [ManagerController::class, 'kamarUpdate'])->name('kamar.update');
        Route::delete('/kamar/{kamar}', [ManagerController::class, 'kamarDestroy'])->name('kamar.destroy');

        // Master Akun Staf
        Route::get('/users', [ManagerController::class, 'userIndex'])->name('users.index');
        Route::post('/users', [ManagerController::class, 'userStore'])->name('users.store');
        Route::post('/users/{user}/toggle', [ManagerController::class, 'userToggleAktif'])->name('users.toggle');
    });

    // Laporan & Rekapitulasi Eksekutif
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/okupansi', [ReportController::class, 'okupansi'])->name('okupansi');
        Route::get('/pendapatan', [ReportController::class, 'pendapatan'])->name('pendapatan');
        Route::get('/riwayat-tamu', [ReportController::class, 'riwayatTamu'])->name('riwayat_tamu');
    });
});
