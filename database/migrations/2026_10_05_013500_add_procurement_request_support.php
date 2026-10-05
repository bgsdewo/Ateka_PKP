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
        Schema::table('requests', function (Blueprint $table) {
            $table->boolean('is_procurement')->default(false)->after('type');
        });

        Schema::table('request_details', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->change();
            $table->string('custom_product_name')->nullable()->after('product_id');
            $table->string('custom_unit')->nullable()->after('custom_product_name');
            $table->string('specification')->nullable()->after('custom_unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_details', function (Blueprint $table) {
            $table->dropColumn(['custom_product_name', 'custom_unit', 'specification']);
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
        });

        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn('is_procurement');
        });
    }
};
