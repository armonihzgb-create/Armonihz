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
        Schema::table('clients', function (Blueprint $table) {
            // 🔥 Añadimos la nueva columna de términos y condiciones
            $table->boolean('terminos_aceptados')->default(false)->after('fcm_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Revertir el cambio por si haces un rollback
            $table->dropColumn('terminos_aceptados');
        });
    }
};