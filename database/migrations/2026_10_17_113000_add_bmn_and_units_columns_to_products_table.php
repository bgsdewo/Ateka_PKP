<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'available_units')) {
                $table->string('available_units')->nullable()->after('unit');
            }
            if (!Schema::hasColumn('products', 'bmn_code')) {
                $table->string('bmn_code', 50)->nullable()->after('code');
            }
            if (!Schema::hasColumn('products', 'bmn_group')) {
                $table->string('bmn_group', 150)->nullable()->after('bmn_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $cols = array_filter(['available_units', 'bmn_code', 'bmn_group'], fn($c) => Schema::hasColumn('products', $c));
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
