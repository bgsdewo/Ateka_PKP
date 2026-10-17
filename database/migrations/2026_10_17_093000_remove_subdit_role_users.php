<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        $subditUserIds = DB::table('users')
            ->where('role', 'subdit')
            ->pluck('id')
            ->toArray();

        if (!empty($subditUserIds)) {
            if (Schema::hasTable('user_notifications')) {
                DB::table('user_notifications')
                    ->whereIn('user_id', $subditUserIds)
                    ->delete();
            }

            if (Schema::hasTable('requests')) {
                $requestIds = DB::table('requests')
                    ->whereIn('user_id', $subditUserIds)
                    ->pluck('id')
                    ->toArray();

                if (!empty($requestIds) && Schema::hasTable('request_details')) {
                    DB::table('request_details')
                        ->whereIn('request_id', $requestIds)
                        ->delete();
                }

                DB::table('requests')
                    ->whereIn('user_id', $subditUserIds)
                    ->delete();
            }

            if (Schema::hasTable('stock_transactions')) {
                DB::table('stock_transactions')
                    ->whereIn('user_id', $subditUserIds)
                    ->delete();
            }

            DB::table('users')
                ->whereIn('id', $subditUserIds)
                ->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Role subdit telah dihapus secara permanen.
    }
};
