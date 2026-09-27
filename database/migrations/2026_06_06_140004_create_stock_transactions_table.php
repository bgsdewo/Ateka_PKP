<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['masuk', 'keluar']);
            $table->integer('quantity');
            $table->integer('stock_after')->default(0);
            $table->date('transaction_date'); // Ini yang wajib diisi controller

            // --- KOLOM BARU SESUAI KARTU PERSEDIAAN ---
            $table->string('no_faktur')->nullable();
            $table->string('uraian')->nullable();
            $table->string('kondisi')->default('Baru');
            $table->decimal('harga_perolehan', 15, 2)->default(0);

            $table->text('note')->nullable(); // Digunakan untuk keterangan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
