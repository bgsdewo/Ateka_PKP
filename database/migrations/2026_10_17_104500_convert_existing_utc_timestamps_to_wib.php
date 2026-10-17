<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Konversi timestamp pada tabel requests & notifications yang sebelumnya tersimpan dalam UTC (+00:00)
     * menjadi Waktu Indonesia Barat / WIB (+07:00).
     */
    public function up(): void
    {
        DB::table('requests')->update([
            'created_at' => DB::raw('DATE_ADD(created_at, INTERVAL 7 HOUR)'),
            'updated_at' => DB::raw('DATE_ADD(updated_at, INTERVAL 7 HOUR)'),
            'verified_at' => DB::raw('CASE WHEN verified_at IS NOT NULL THEN DATE_ADD(verified_at, INTERVAL 7 HOUR) ELSE NULL END'),
            'distributed_at' => DB::raw('CASE WHEN distributed_at IS NOT NULL THEN DATE_ADD(distributed_at, INTERVAL 7 HOUR) ELSE NULL END'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('requests')->update([
            'created_at' => DB::raw('DATE_SUB(created_at, INTERVAL 7 HOUR)'),
            'updated_at' => DB::raw('DATE_SUB(updated_at, INTERVAL 7 HOUR)'),
            'verified_at' => DB::raw('CASE WHEN verified_at IS NOT NULL THEN DATE_SUB(verified_at, INTERVAL 7 HOUR) ELSE NULL END'),
            'distributed_at' => DB::raw('CASE WHEN distributed_at IS NOT NULL THEN DATE_SUB(distributed_at, INTERVAL 7 HOUR) ELSE NULL END'),
        ]);
    }
};
