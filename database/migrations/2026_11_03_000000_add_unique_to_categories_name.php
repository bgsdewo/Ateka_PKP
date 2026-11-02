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
        // Pastikan duplikat id > 56 terhapus sebelum menambahkan unique index jika belum
        $dupCount = DB::table('categories')->where('id', '>', 56)->count();
        $prodDups = DB::table('products')->where('category_id', '>', 56)->count();
        if ($dupCount > 0 && $prodDups === 0) {
            DB::table('categories')->where('id', '>', 56)->delete();
        }

        $indexes = DB::select("SHOW INDEX FROM categories WHERE Key_name = 'categories_name_unique'");
        if (count($indexes) === 0) {
            Schema::table('categories', function (Blueprint $table) {
                $table->unique('name', 'categories_name_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique('categories_name_unique');
        });
    }
};
