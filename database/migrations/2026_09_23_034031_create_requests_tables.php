<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Utama Pengajuan (Header)
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Siapa yang mengajukan (Pegawai / Subdit)
            $table->string('request_code')->unique(); // Kode unik pengajuan, misal: REQ-20260323-001
            $table->enum('type', ['individu', 'subdit']); // Jenis pengajuan
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Status approval Kasubag TU
            $table->text('notes')->nullable(); // Keterangan tambahan dari peminta
            $table->text('rejection_reason')->nullable(); // Alasan jika ditolak oleh Bu Utami
            $table->timestamps();
        });

        // Tabel Detail Barang yang Diminta
        Schema::create('request_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('requests')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Barang ATK yang diminta
            $table->integer('quantity'); // Jumlah yang diminta
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_details');
        Schema::dropIfExists('requests');
    }
};
