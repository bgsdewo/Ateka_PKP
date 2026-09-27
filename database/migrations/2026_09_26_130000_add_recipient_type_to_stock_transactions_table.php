<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->string('recipient_type', 20)->nullable()->after('type');
        });

        // Backfill existing data based on note
        DB::table('stock_transactions')
            ->where('type', 'keluar')
            ->where(function ($q) {
                $q->where('note', 'like', '%Subdit%')
                  ->orWhere('uraian', 'like', '%Subdit%');
            })
            ->update(['recipient_type' => 'subdit']);

        DB::table('stock_transactions')
            ->where('type', 'keluar')
            ->whereNull('recipient_type')
            ->update(['recipient_type' => 'individu']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropColumn('recipient_type');
        });
    }
};
