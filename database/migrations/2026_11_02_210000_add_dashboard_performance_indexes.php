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
        // 1. Index untuk products.stock
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'stock')) {
            $existing = collect(DB::select("SHOW INDEX FROM products"))->pluck('Key_name')->all();
            if (!in_array('idx_products_stock', $existing)) {
                Schema::table('products', function (Blueprint $table) {
                    $table->index('stock', 'idx_products_stock');
                });
            }
        }

        // 2. Index untuk requests(status, is_procurement)
        if (Schema::hasTable('requests') && Schema::hasColumn('requests', 'status') && Schema::hasColumn('requests', 'is_procurement')) {
            $existing = collect(DB::select("SHOW INDEX FROM requests"))->pluck('Key_name')->all();
            if (!in_array('idx_requests_status_procurement', $existing)) {
                Schema::table('requests', function (Blueprint $table) {
                    $table->index(['status', 'is_procurement'], 'idx_requests_status_procurement');
                });
            }
        }

        // 3. Index untuk request_details(request_id, is_fulfilled)
        if (Schema::hasTable('request_details') && Schema::hasColumn('request_details', 'request_id') && Schema::hasColumn('request_details', 'is_fulfilled')) {
            $existing = collect(DB::select("SHOW INDEX FROM request_details"))->pluck('Key_name')->all();
            if (!in_array('idx_req_details_req_fulfilled', $existing)) {
                Schema::table('request_details', function (Blueprint $table) {
                    $table->index(['request_id', 'is_fulfilled'], 'idx_req_details_req_fulfilled');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products')) {
            $existing = collect(DB::select("SHOW INDEX FROM products"))->pluck('Key_name')->all();
            if (in_array('idx_products_stock', $existing)) {
                Schema::table('products', function (Blueprint $table) {
                    $table->dropIndex('idx_products_stock');
                });
            }
        }

        if (Schema::hasTable('requests')) {
            $existing = collect(DB::select("SHOW INDEX FROM requests"))->pluck('Key_name')->all();
            if (in_array('idx_requests_status_procurement', $existing)) {
                Schema::table('requests', function (Blueprint $table) {
                    $table->dropIndex('idx_requests_status_procurement');
                });
            }
        }

        if (Schema::hasTable('request_details')) {
            $existing = collect(DB::select("SHOW INDEX FROM request_details"))->pluck('Key_name')->all();
            if (in_array('idx_req_details_req_fulfilled', $existing)) {
                Schema::table('request_details', function (Blueprint $table) {
                    $table->dropIndex('idx_req_details_req_fulfilled');
                });
            }
        }
    }
};
