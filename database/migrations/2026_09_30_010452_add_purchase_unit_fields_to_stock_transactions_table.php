<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->string('satuan_beli', 50)->nullable()->after('quantity');
            $table->integer('jumlah_beli')->nullable()->after('satuan_beli');
            $table->integer('isi_per_kemasan')->nullable()->after('jumlah_beli');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropColumn(['satuan_beli', 'jumlah_beli', 'isi_per_kemasan']);
        });
    }
};
