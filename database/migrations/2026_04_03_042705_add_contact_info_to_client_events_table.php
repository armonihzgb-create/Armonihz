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
        Schema::table('client_events', function (Blueprint $table) {
            // Agregamos las columnas de email y telefono
            // Usamos nullable() por si el usuario de Firebase no tiene alguno de los dos
            $table->string('email')->nullable()->after('presupuesto');
            $table->string('telefono')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_events', function (Blueprint $table) {
            // Eliminamos las columnas si hacemos un rollback
            $table->dropColumn(['email', 'telefono']);
        });
    }
};