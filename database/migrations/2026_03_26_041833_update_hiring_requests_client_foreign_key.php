<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hiring_requests', function (Blueprint $table) {
            // 1. Soltamos la llave vieja que apunta a "users"
            $table->dropForeign(['client_id']);
            
            // 2. Creamos la nueva llave que apunta a "clients"
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('hiring_requests', function (Blueprint $table) {
            // Revertir si hay error
            $table->dropForeign(['client_id']);
            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};