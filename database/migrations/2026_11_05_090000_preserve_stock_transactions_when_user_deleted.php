<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sebelumnya stock_transactions.user_id memakai cascadeOnDelete(): menghapus satu akun
 * pengguna ikut menghapus SEMUA mutasi stok (barang masuk/keluar) yang dicatat akun itu,
 * sehingga kartu persediaan & laporan bulanan kehilangan baris. Sekarang riwayat mutasi
 * dipertahankan dan user_id menjadi NULL saat akun dihapus.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });

        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });

        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
