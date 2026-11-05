<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('products', 'khusus_subdit')) {
            Schema::table('products', function (Blueprint $table) {
                $table->boolean('khusus_subdit')->default(false)->after('description');
            });
        }

        // Tandai barang yang sudah ada sesuai kata kunci awal.
        $keywords = [
            'pembolong kertas',
            'toner',
            'epson ink printer',
            'cannon blanck ink',
            'canon black ink',
            'canon blank ink',
        ];

        DB::table('products')->where(function ($q) use ($keywords) {
            foreach ($keywords as $kw) {
                $q->orWhereRaw('LOWER(name) LIKE ?', ['%' . $kw . '%']);
            }
        })->update(['khusus_subdit' => true]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'khusus_subdit')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('khusus_subdit');
            });
        }
    }
};
