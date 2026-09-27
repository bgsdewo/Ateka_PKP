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
        // Alter status column to varchar so it supports 'distributed' alongside 'pending', 'approved', 'rejected'
        Schema::table('requests', function (Blueprint $table) {
            $table->string('status', 30)->default('pending')->change();
            $table->timestamp('distributed_at')->nullable()->after('rejection_reason');
            $table->foreignId('distributed_by')->nullable()->after('distributed_at')->constrained('users')->nullOnDelete();
            $table->string('receiver_name')->nullable()->after('distributed_by');
            $table->text('distribution_notes')->nullable()->after('receiver_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropForeign(['distributed_by']);
            $table->dropColumn(['distributed_at', 'distributed_by', 'receiver_name', 'distribution_notes']);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->change();
        });
    }
};
