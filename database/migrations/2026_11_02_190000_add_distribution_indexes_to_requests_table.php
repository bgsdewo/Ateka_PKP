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
        Schema::table('requests', function (Blueprint $table) {
            $existingIndexes = collect(DB::select("SHOW INDEX FROM requests"))
                ->pluck('Key_name')
                ->unique()
                ->toArray();

            if (!in_array('idx_requests_status_type', $existingIndexes)) {
                $table->index(['status', 'type'], 'idx_requests_status_type');
            }
            if (!in_array('idx_requests_status_dates', $existingIndexes)) {
                $table->index(['status', 'distributed_at', 'created_at'], 'idx_requests_status_dates');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $existingIndexes = collect(DB::select("SHOW INDEX FROM requests"))
                ->pluck('Key_name')
                ->unique()
                ->toArray();

            if (in_array('idx_requests_status_type', $existingIndexes)) {
                $table->dropIndex('idx_requests_status_type');
            }
            if (in_array('idx_requests_status_dates', $existingIndexes)) {
                $table->dropIndex('idx_requests_status_dates');
            }
        });
    }
};
