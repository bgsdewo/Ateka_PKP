<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('products', 'khusus_subdit')) {
            return;
        }

        // Barang baterai ikut ditandai "Khusus Perwakilan Subdit".
        DB::table('products')
            ->whereRaw('LOWER(name) LIKE ?', ['%baterai%'])
            ->update(['khusus_subdit' => true]);
    }

    public function down(): void
    {
        // Tidak ada yang dikembalikan: penanda dapat diubah manual lewat Edit Barang.
    }
};
