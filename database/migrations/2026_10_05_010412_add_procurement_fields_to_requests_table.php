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
            $table->string('purchase_invoice')->nullable()->after('verification_notes');
            $table->date('purchase_date')->nullable()->after('purchase_invoice');
            $table->decimal('purchase_price', 15, 2)->nullable()->after('purchase_date');
            $table->text('purchase_notes')->nullable()->after('purchase_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn(['purchase_invoice', 'purchase_date', 'purchase_price', 'purchase_notes']);
        });
    }
};
