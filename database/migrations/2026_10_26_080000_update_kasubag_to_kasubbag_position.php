<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('users')
            ->where('position', 'Kasubag TU')
            ->update(['position' => 'Kasubbag TU']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')
            ->where('position', 'Kasubbag TU')
            ->update(['position' => 'Kasubag TU']);
    }
};
