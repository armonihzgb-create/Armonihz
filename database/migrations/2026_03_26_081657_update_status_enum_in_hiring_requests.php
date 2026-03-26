<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Actualizamos la columna para que acepte 'counter_offer'
        DB::statement("ALTER TABLE hiring_requests MODIFY status ENUM('pending', 'accepted', 'rejected', 'counter_offer') DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Revertir (quitar counter_offer) si es necesario
        DB::statement("ALTER TABLE hiring_requests MODIFY status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending'");
    }
};