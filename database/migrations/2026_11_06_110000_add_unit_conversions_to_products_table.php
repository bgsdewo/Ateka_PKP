<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('products', 'unit_conversions')) {
            Schema::table('products', function (Blueprint $table) {
                // Isi per kemasan: {"Dus": 5, "Pak": 12} = 1 Dus = 5 satuan dasar (products.unit), dst.
                $table->json('unit_conversions')->nullable()->after('available_units');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'unit_conversions')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('unit_conversions');
            });
        }
    }
};
