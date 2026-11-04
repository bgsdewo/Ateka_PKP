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
            $existingIndexes = collect(DB::select("SHOW INDEX FROM stock_transactions"))
                ->pluck('Key_name')
                ->unique()
                ->toArray();

            if (!in_array('idx_stock_tx_type_date', $existingIndexes)) {
                $table->index(['type', 'transaction_date', 'id'], 'idx_stock_tx_type_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $existingIndexes = collect(DB::select("SHOW INDEX FROM stock_transactions"))
                ->pluck('Key_name')
                ->unique()
                ->toArray();

            if (in_array('idx_stock_tx_type_date', $existingIndexes)) {
                $table->dropIndex('idx_stock_tx_type_date');
            }
        });
    }
};
