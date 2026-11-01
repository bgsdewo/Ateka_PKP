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
use App\Http\Controllers\RequestRecapController;
use App\Http\Controllers\NotificationController;

// Default Route
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'authenticate'])
        ->middleware('throttle:5,1')
        ->name('login.process');

    // ==========================================
    // ROUTE REGISTRASI AKUN BARU
    // ==========================================
    Route::get('/register', [AuthController::class, 'register'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'storeRegister'])
        ->middleware('throttle:5,1')
        ->name('register.store');

    // ==========================================
    // ROUTE LUPA PASSWORD DENGAN NIP & EMAIL
    // ==========================================
    Route::get('/lupa-password', [AuthController::class, 'showForgotPassword'])
        ->name('password.forgot');

    Route::post('/lupa-password', [AuthController::class, 'resetPasswordWithNip'])
        ->middleware('throttle:3,1')
        ->name('password.reset.nip');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // ==========================================
    // ROUTE NOTIFIKASI
    // ==========================================
    Route::get('/notifikasi', [NotificationController::class, 'index'])
        ->name('notifications.index');
    Route::post('/notifikasi/{id}/baca', [NotificationController::class, 'markAsRead'])
        ->name('notifications.markAsRead');
    Route::post('/notifikasi/baca-batch', [NotificationController::class, 'markBatchAsRead'])
        ->name('notifications.markBatchAsRead');
    Route::post('/notifikasi/baca-semua', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.markAllAsRead');

    // Master Data
    Route::prefix('master-data')->name('master-data.')->group(function () {
        Route::resource('/kategori-barang', CategoryController::class)
            ->middleware('role:kasubag_tu');

        Route::resource('/daftar-barang', ProductController::class)
            ->middleware('role:kasubag_tu,pegawai_gudang');

        Route::resource('/manajemen-pengguna', UserManagementController::class)
            ->middleware('role:kasubag_tu');
    });

    // Operasional Gudang
    Route::middleware('role:kasubag_tu,pegawai_gudang')->group(function () {
        Route::get('/barang-masuk', [BarangMasukController::class, 'index'])
            ->name('barang-masuk');

        Route::get('/barang-keluar', [BarangKeluarController::class, 'index'])
            ->name('barang-keluar');

        Route::post('/persediaan-barang/transaksi', [InventoryController::class, 'storeTransaction'])
            ->name('inventory.transaction.store');
    });

    // ==========================================
    // 1. ROUTE VERIFIKASI & PERSETUJUAN - KASUBAG TU
    // ==========================================
    Route::middleware('role:kasubag_tu,admin')->group(function () {
        Route::get('/persetujuan', [ApprovalController::class, 'index'])
            ->name('persetujuan.index');

        Route::get('/verifikasi', [ApprovalController::class, 'index'])
            ->name('verifikasi.index');

        Route::put('/persetujuan/{id}', [ApprovalController::class, 'updateStatus'])
            ->name('persetujuan.update');

        Route::put('/verifikasi/{id}', [ApprovalController::class, 'updateStatus'])
            ->name('verifikasi.update');

        Route::post('/persetujuan/{id}/procure', [ApprovalController::class, 'procureAndApprove'])
            ->name('persetujuan.procure');

        Route::post('/verifikasi/{id}/procure', [ApprovalController::class, 'procureAndApprove'])
            ->name('verifikasi.procure');

        // Rekapitulasi Permintaan Subbag TU
        Route::get('/rekapitulasi-permintaan', [RequestRecapController::class, 'index'])
            ->name('rekapitulasi-permintaan.index');
    });

    // ==========================================
    // 1B. CETAK DOKUMEN REKAPITULASI (OTORISASI KONTROL DI CONTROLLER)
    // ==========================================
    Route::get('/rekapitulasi-permintaan/cetak/{id?}', [RequestRecapController::class, 'printRecap'])
        ->name('rekapitulasi-permintaan.cetak');

    // ==========================================
    // 2. ROUTE KOORDINASI & DISTRIBUSI - PETUGAS DISTRIBUSI / KASUBAG TU
    // ==========================================
    Route::middleware('role:kasubag_tu,pegawai_gudang')->group(function () {
        Route::get('/distribusi', [DistributionController::class, 'index'])
            ->name('distribusi.index');

        Route::post('/distribusi/{id}/handover', [DistributionController::class, 'confirmHandover'])
            ->name('distribusi.handover');

        Route::post('/distribusi/{id}/upload-bukti', [DistributionController::class, 'uploadProof'])
            ->name('distribusi.uploadProof');
    });

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

    Route::post('/pengajuan/{id}/upload-signed-proof', [RequestController::class, 'uploadSignedProof'])
        ->name('pengajuan.uploadSignedProof');

    Route::get('/pengajuan/pengadaan', [RequestController::class, 'createProcurement'])
        ->name('pengajuan.pengadaan.create');

    Route::post('/pengajuan/pengadaan', [RequestController::class, 'storeProcurement'])
        ->name('pengajuan.pengadaan.store');

    Route::get('/katalog-atk', [ProductController::class, 'catalog'])
        ->name('katalog-atk');

    Route::post('/katalog-atk/pesan', [RequestController::class, 'orderFromCatalog'])
        ->name('katalog.pesan');

    // ==========================================
    // 4. ROUTE PELAPORAN & ARSIP - KASUBAG TU & PEGAWAI GUDANG
    // ==========================================
    Route::middleware('role:kasubag_tu,pegawai_gudang')->group(function () {
        Route::get('/laporan', [ReportController::class, 'index'])
            ->name('reports.index');

        Route::get('/laporan/cetak', [ReportController::class, 'printReport'])
            ->name('reports.print');

        Route::get('/laporan/rekapitulasi', [ReportController::class, 'rekapitulasi'])
            ->name('reports.rekapitulasi');

        Route::get('/laporan/berita-acara', [ReportController::class, 'beritaAcara'])
            ->name('reports.berita-acara');

        Route::get('/laporan/kartu-persediaan', [ReportController::class, 'kartuPersediaan'])
            ->name('reports.kartu-persediaan');

        Route::get('/laporan/kartu-persediaan/cetak', [ReportController::class, 'cetakKartuPersediaan'])
            ->name('reports.kartu-persediaan.cetak');
    });

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

// ==========================================
// ROUTE SINKRONISASI BMN & DATABASE MAINTENANCE (HOSTING & LOCAL)
// ==========================================
Route::get('/system/sync-bmn-database', function (\Illuminate\Http\Request $request) {
    $secret = 'ateka_sync_2026';
    $isAuthorized = (auth()->check() && in_array(auth()->user()->role, ['kasubag_tu', 'pegawai_gudang', 'admin']))
                    || ($request->query('token') === $secret);

    if (!$isAuthorized) {
        return response()->json([
            'status' => 'error',
            'message' => 'Akses ditolak. Silakan login sebagai Kasubbag TU / Pegawai Gudang, atau sertakan token otorisasi yang valid (?token=ateka_sync_2026).'
        ], 403);
    }

    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $migrateOutput = \Illuminate\Support\Facades\Artisan::output();

        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'Lembar2AtkCatalogSeeder', '--force' => true]);
        $seedOutput = \Illuminate\Support\Facades\Artisan::output();

        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        $clearOutput = \Illuminate\Support\Facades\Artisan::output();

        return response()->json([
            'status' => 'success',
            'message' => 'Sinkronisasi database BMN, Kategori Barang, dan Daftar Barang berhasil!',
            'details' => [
                'migration' => trim($migrateOutput),
                'seeder' => trim($seedOutput),
                'cache' => trim($clearOutput),
            ],
            'timestamp' => now()->toDateTimeString(),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal melakukan sinkronisasi: ' . $e->getMessage(),
        ], 500);
    }
})->name('system.sync-bmn-database');

