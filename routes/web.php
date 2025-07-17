<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\JenisPembayaranController;
use App\Http\Controllers\Admin\TagihanController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\KlasifikasiController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\TagihanController as StudentTagihanController;
use App\Http\Controllers\Student\PembayaranController as StudentPembayaranController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes (dari Laravel Breeze)
require __DIR__ . '/auth.php';

// Redirect after login based on role
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('student.dashboard');
    })->name('dashboard');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Data Master
    Route::resource('siswa', SiswaController::class);
    Route::resource('jenis-pembayaran', JenisPembayaranController::class);

    // Tagihan Management
    Route::resource('tagihan', TagihanController::class);
    Route::post('tagihan/generate-bulk', [TagihanController::class, 'generateBulk'])->name('tagihan.generate-bulk');

    // Pembayaran Management
    Route::resource('pembayaran', PembayaranController::class);
    Route::patch('pembayaran/{pembayaran}/confirm', [PembayaranController::class, 'confirm'])->name('pembayaran.confirm');
    Route::patch('pembayaran/{pembayaran}/reject', [PembayaranController::class, 'reject'])->name('pembayaran.reject');

    // Laporan
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan');
    Route::get('laporan/export/{type}', [LaporanController::class, 'export'])->name('laporan.export');

    // Klasifikasi Naive Bayes
    Route::get('klasifikasi', [KlasifikasiController::class, 'index'])->name('klasifikasi');
    Route::post('klasifikasi/run', [KlasifikasiController::class, 'runClassification'])->name('klasifikasi.run');
});

// Student Routes
Route::middleware(['auth', 'student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

    // Tagihan
    Route::get('tagihan', [StudentTagihanController::class, 'index'])->name('tagihan');
    Route::get('tagihan/{tagihan}', [StudentTagihanController::class, 'show'])->name('tagihan.show');
    Route::get('tagihan/{tagihan}/pay', [StudentTagihanController::class, 'pay'])->name('tagihan.pay');
    Route::post('tagihan/{tagihan}/pay', [StudentTagihanController::class, 'storePay'])->name('tagihan.store-pay');

    // Pembayaran History
    Route::get('pembayaran', [StudentPembayaranController::class, 'index'])->name('pembayaran');
    Route::get('pembayaran/{pembayaran}', [StudentPembayaranController::class, 'show'])->name('pembayaran.show');
    Route::get('pembayaran/{pembayaran}/download', [StudentPembayaranController::class, 'download'])->name('pembayaran.download');
});

// Profile Routes (untuk semua user)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
