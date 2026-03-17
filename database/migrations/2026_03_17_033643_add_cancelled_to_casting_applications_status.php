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
        // Add 'cancelled' to the ENUM list
        DB::statement("ALTER TABLE casting_applications MODIFY COLUMN status ENUM('pending', 'accepted', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Attempt to revert back (WARNING: if any rows have 'cancelled' this might fail in strict mode)
        DB::statement("ALTER TABLE casting_applications MODIFY COLUMN status ENUM('pending', 'accepted', 'rejected') NOT NULL DEFAULT 'pending'");
    }
};
