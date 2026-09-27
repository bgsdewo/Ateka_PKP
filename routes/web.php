<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\DistributionController;

// Default Route
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'authenticate'])
        ->name('login.process');

    // ==========================================
    // ROUTE REGISTRASI AKUN BARU
    // ==========================================
    Route::get('/register', [AuthController::class, 'register'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'storeRegister'])
        ->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Master Data
    Route::prefix('master-data')->name('master-data.')->group(function () {
        Route::resource('/kategori-barang', CategoryController::class);
        Route::resource('/daftar-barang', ProductController::class);
        Route::resource('/manajemen-pengguna', UserManagementController::class);
    });

    // Operasional Gudang
    Route::get('/barang-masuk', [BarangMasukController::class, 'index'])
        ->name('barang-masuk');

    Route::get('/barang-keluar', [BarangKeluarController::class, 'index'])
        ->name('barang-keluar');

    Route::post('/persediaan-barang/transaksi', [InventoryController::class, 'storeTransaction'])
        ->name('inventory.transaction.store');

    // ==========================================
    // 1. ROUTE PERSETUJUAN (APPROVAL) - KASUBAG TU
    // ==========================================
    Route::get('/persetujuan', [ApprovalController::class, 'index'])
        ->name('persetujuan.index');

    Route::put('/persetujuan/{id}', [ApprovalController::class, 'updateStatus'])
        ->name('persetujuan.update');

    // ==========================================
    // 2. ROUTE KOORDINASI & DISTRIBUSI - PIC DISTRIBUSI
    // ==========================================
    Route::get('/distribusi', [DistributionController::class, 'index'])
        ->name('distribusi.index');

    Route::post('/distribusi/{id}/handover', [DistributionController::class, 'confirmHandover'])
        ->name('distribusi.handover');

    Route::get('/distribusi/{id}/bukti', [DistributionController::class, 'printBukti'])
        ->name('distribusi.bukti');

    // ==========================================
    // 3. ROUTE PENGAJUAN ATK & KATALOG - PEGAWAI / SUBDIT
    // ==========================================
    Route::get('/pengajuan', [RequestController::class, 'index'])
        ->name('pengajuan.index');

    Route::get('/pengajuan/create', [RequestController::class, 'create'])
        ->name('pengajuan.create');

    Route::post('/pengajuan', [RequestController::class, 'store'])
        ->name('pengajuan.store');

    Route::get('/katalog-atk', [ProductController::class, 'catalog'])
        ->name('katalog-atk');

    // ==========================================
    // 4. ROUTE PELAPORAN & ARSIP - ARSIPARIS / KASUBAG TU
    // ==========================================
    Route::get('/laporan', [ReportController::class, 'index'])
        ->name('reports.index');

    Route::get('/laporan/rekapitulasi', [ReportController::class, 'rekapitulasi'])
        ->name('reports.rekapitulasi');

    Route::get('/laporan/berita-acara', [ReportController::class, 'beritaAcara'])
        ->name('reports.berita-acara');

    // Profil & Akun
    Route::get('/profil', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/profil', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::get('/ubah-password', [ProfileController::class, 'password'])
        ->name('profile.password');

    Route::put('/ubah-password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password.update');
});
